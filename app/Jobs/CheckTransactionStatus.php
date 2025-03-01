<?php

namespace App\Jobs;

use App\Models\BillPayment;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\NotificationService;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CheckTransactionStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transactionId;
    public $tid;
    public $maxRetries = 3; // Maximum retries
    public $currentRetry; // Tracks current retry count
    public $accessToken;
    protected $NotificationService;
    public $customerName;
    public $customerEmail;


    /**
     * Create a new job instance.
     */
    public function __construct($transactionId, $tid, $currentRetry = 0, $customerName = null, $customerEmail = null)
    {
        $this->transactionId = $transactionId;
        $this->currentRetry = $currentRetry;
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
        $this->tid = $tid;
        $this->accessToken = config('access_token.live_token');
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->NotificationService = app(NotificationService::class);
        $mainTransaction = Transaction::where('id', $this->tid)->first();
        $transaction = BillPayment::where('refference', $this->transactionId)->first();
        $userId = $transaction->user_id;
        $wallet = Wallet::where('user_id', $userId)->orderBy('id', 'desc')->first();
        if (!$transaction) {
            Log::warning("Transaction not found: {$this->transactionId}");
            return;
        }

        $response = Http::withHeaders(['AccessToken' => $this->accessToken])
            ->get('https://api-apps.vfdbank.systems/vtech-wallet/api/v1/billspaymentstore/transactionStatus', [
                'transactionId' => $this->transactionId,
            ]);

        if ($response->successful()) {
            $status = $response->json('data.transactionStatus');
            if ($status === '00') {
                $token = '';
                $mainTransaction->update(['status' => 'completed']);
                if (isset($response->json()['data']['token'])) {
                    $token = $response->json()['data']['token'];
                    $formattedToken = implode('-', str_split($token, 4));
                    Mail::send(
                        'emails.electricity_token',
                        [
                            'customerName' => $this->customerName,
                            'tokenNumber' => $formattedToken, 
                        ],
                        function ($message) {
                            $message->to($this->customerEmail)
                                ->subject('Your Electricity Token');
                        }
                    );
                }

                $transaction->update(['status' => 'success', 'token' => $token]);
                Log::info("Transaction successful: {$this->transactionId}");
                $notification = new Notification();
                $notification->title = "Bill Payment Successful";
                $notification->type = "billPayment";
                $notification->message = "Bill payment of " . $transaction->amount . "NGN has been successful";
                $notification->user_id = $userId;
                $notification->icon = asset('notificationLogos/bill.png');
                $notification->iconColor = config('notification_colors.colors.Bill');
                $notification->save();
                $notificationTitle = "Bill Payment Successful";
                $notificationMessage = "Bill payment of " . $transaction->amount . " has been successful";
                $notificationResponse = $this->NotificationService->sendToUserById($userId, $notificationTitle, $notificationMessage);
                Log::info('Notification Response: ', $notificationResponse);
            } else if ($status === '99') {
                $transaction->update(['status' => 'failed']);
                Log::info("Transaction failed: {$this->transactionId}");
                $mainTransaction->update(attributes: ['status' => 'failed']);
                $wallet->accountBalance += $transaction->totalAmount;
                $wallet->save();
                $notification = new Notification();
                $notification->title = "Bill Payment Failed";
                $notification->type = "billPayment";
                $notification->message = "Bill payment of " . $transaction->amount . "NGN has been failed. We have refunded your account";
                $notification->user_id = $userId;
                $notification->icon = asset('notificationLogos/bill.png');
                $notification->iconColor = config('notification_colors.colors.Bill');
                $notification->save();
                $notificationTitle = "Bill Payment Failed";
                $notificationMessage = "Bill payment of " . $transaction->amount . " has been Failed.";
                $notificationResponse = $this->NotificationService->sendToUserById($userId, $notificationTitle, $notificationMessage);
            } else {
                if ($this->currentRetry < $this->maxRetries) {
                    $this->retryJob();
                } else {
                    Log::info("Transaction still pending after max retries: {$this->transactionId}");
                }
            }
        } else {
            Log::error("Failed to fetch transaction status: {$this->transactionId}", $response->json());
            if ($this->currentRetry < $this->maxRetries) {
                $this->retryJob();
            }
        }
    }

    /**
     * Retry the job after a delay.
     */
    protected function retryJob()
    {
        $nextRetry = $this->currentRetry + 1;
        CheckTransactionStatus::dispatch($this->transactionId, $this->tid, $nextRetry)->delay(now()->addMinute());
        Log::info("Retrying transaction check for: {$this->transactionId}, attempt: {$nextRetry}");
    }
}
