<?php

namespace App\Jobs;

use App\Models\BillPayment;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CheckTransactionStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transactionId;
    public $tid;
    public $maxRetries = 3; // Maximum retries
    public $currentRetry; // Tracks current retry count
    public $accessToken;

    /**
     * Create a new job instance.
     */
    public function __construct($transactionId, $tid, $currentRetry = 0)
    {
        $this->transactionId = $transactionId;
        $this->currentRetry = $currentRetry;
        $this->tid = $tid;
        $this->accessToken = config('access_token.live_token');
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $mainTransaction = Transaction::where('id', $this->tid)->first();
        $transaction = BillPayment::where('refference', $this->transactionId)->first();

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
                }
                $transaction->update(['status' => 'success', 'token' => $token]);
                Log::info("Transaction successful: {$this->transactionId}");
            } else {
                if ($this->currentRetry < $this->maxRetries) {
                    $this->retryJob();
                } else {
                    $transaction->update(['status' => 'pending', 'last_checked_at' => now()]);
                    Log::info("Transaction still pending after max retries: {$this->transactionId}");
                }
            }
        } else {
            Log::error("Failed to fetch transaction status: {$this->transactionId}", $response->json());
        }
    }

    /**
     * Retry the job after a delay.
     */
    protected function retryJob()
    {
        $nextRetry = $this->currentRetry + 1;
        CheckTransactionStatus::dispatch($this->transactionId, $nextRetry)->delay(now()->addMinute());
        Log::info("Retrying transaction check for: {$this->transactionId}, attempt: {$nextRetry}");
    }
}
