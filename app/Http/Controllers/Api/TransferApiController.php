<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\FreeFund;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\VirtualAccountHistory;
use App\Models\Wallet;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransferApiController extends Controller
{
    public $accessToken;
    protected $NotificationService;

    public function __construct(NotificationService $NotificationService)
    {
        $this->NotificationService = $NotificationService;
        $this->accessToken = config('access_token.live_token');
        // $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiZGE1YjM5ZDItMGE2MS00MGE5LTg2ZGYtNTFjNDE5NmU4MmMyIiwiaWF0IjoxNzMxOTIyNjMyLCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.D8lFZCna6PZNIXnmJt-Xwc2JJ9rYxNPv4x5yDwRnldGs6tZu8KAlCoXumVIcXuUrOvcEud0hSIkQ7hZUjsFh7Q';
    }
    public function beneficiaryEnquiry(Request $request)
    {
        // Validate the incoming request parameters
        $validator = Validator::make($request->all(), [
            'accountNo' => 'required|string',
            'bank' => 'required|string',
            'transfer_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'data' => [],
            ], 400);
        }

        // Prepare the API request
        $accountNo = $request->input('accountNo');
        $bank = $request->input('bank');
        $transferType = $request->input('transfer_type');

        // Make the API call to fetch the beneficiary details
        $response = Http::withHeaders([
            'AccessToken' => $this->accessToken,
        ])->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/transfer/recipient', [
            'accountNo' => $accountNo,
            'bank' => $bank,
            'transfer_type' => $transferType,
        ]);
        // Check if the API request was successful
        if ($response->successful()) {
            // Check if the account number exists in the database
            Log::info('API Response: beneficiaryEnquiry', $response->json());
            $userAccount = Account::where('account_number', $accountNo)->with('user')->first();

            if ($userAccount) {
                // Account found, send additional user details
                $profilePictureUrl = asset('storage/' . $userAccount->profile_picture);
                $beneficeiryDetails = $response->json('data');
                return response()->json([
                    'status' => 'success',
                    'message' => 'Beneficiary details retrieved successfully',
                    'data' => array_merge($beneficeiryDetails, [ // Merge beneficiary details directly
                        'firstName' => $userAccount->firstName,
                        'lastName' => $userAccount->lastName,
                        'email' => $userAccount->user->email,
                        'profilePicture' => $profilePictureUrl
                    ]),
                ], 200);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Beneficiary details retrieved successfully',
                    'additionalMessage' => 'Beneficiary account not found in our records.',
                    'data' => $response->json('data'), // Return the beneficiary details from the API
                ], 200);
            }
        } else {
            Log::info('API Error Response: beneficiaryEnquiry', $response->json());
            return response()->json([
                'status' => 'error',
                'message' => $response->json(key: 'message'), // Error message from the API
                'data' => $response->json('data'),
            ], $response->status());
        }
    }

    public function transferFunds(Request $request)
    {
        // Validate the request parameters
        $validator = Validator::make($request->all(), [
            'toClientName' => 'required|string',
            'toAccount' => 'required|string',
            'amount' => 'required|numeric',
            'toBank' => 'required|string',
            'transferType' => 'required|string|in:intra,inter',
            'toClientId' => 'required|string',
            'toClient' => 'required|string',
            'toSavingsId' => 'required|string',
            'toSession' => 'nullable|string',
            'toBvn' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 400);
        }

        $reference = 'mxPay-' . mt_rand(1000, 9999);
        $fromAccount = "1001629262"; // This should ideally come from the authenticated user or a dynamic source
        $fromClientId = "149383"; // This should also come dynamically based on the logged-in user
        $fromClient = "Mx Bill Pay"; // Adjust according to your requirements
        $fromSavingsId = "162926"; // Adjust according to your requirements
        $fromBvn = "22222222226"; // Adjust according to your requirements

        // Prepare the payload
        $payload = [
            'fromAccount' => $fromAccount,
            'toAccount' => $request->toAccount,
            'amount' => $request->amount,
            'toBank' => $request->toBank,
            'transferType' => $request->transferType,
            'fromClientId' => $fromClientId,
            'fromClient' => $fromClient,
            'fromSavingsId' => $fromSavingsId,
            'fromBvn' => $fromBvn,
            'toClientId' => $request->toClientId,
            'toClient' => $request->toClient,
            'toSavingsId' => $request->toSavingsId,
            'toSession' => $request->toSession,
            'toBvn' => $request->toBvn,
            'signature' => $this->generateSignature($fromAccount, $request->toAccount),
            'reference' => $reference,
        ];

        // Make the API request with a retry mechanism
        $maxRetries = 3; // Number of retries
        $attempt = 0;
        $response = null;

        while ($attempt < $maxRetries) {
            try {
                $response = Http::withHeaders([
                    'AccessToken' => $this->accessToken,
                ])->post('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/transfer', $payload);

                // Break the loop if the request is successful
                if ($response->successful()) {
                    break;
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error('Connection error while making transfer', ['error' => $e->getMessage()]);
            }
            $attempt++;
            sleep(1); // Wait before retrying
        }

        // Check if the API request was successful
        if ($response && $response->successful() && $response->json()['status'] == "00") {
            $responseData = $response->json();
            log::info('API Response:', $responseData);
            // Record the successful transaction
            $this->recordTransaction($request, 'Completed', $reference, $responseData['data']);

            // Check if the transfer type is intra
            if ($request->transferType === 'intra') {
                $beneficiaryAccount = Account::where('account_number', $request->toAccount)->first();
                if ($beneficiaryAccount) {
                    // Record the incoming funds for the beneficiary
                    $this->recordIncomingFunds($beneficiaryAccount->user_id, $request->amount, $reference, $beneficiaryAccount, $request);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Transfer successful',
                'data' => [
                    'beneficiaryAccountNumber' => $request->toAccount,
                    'paymentReference' => $reference,
                    'clientName' => $request->toClientName,
                ],
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch the transfer response after multiple attempts.',
                'data' => $response ? $response->json() : null,
            ], 500);
        }
    }

    private function recordTransaction(Request $request, $status, $reference, $responseData = null)
    {

        //
        $notification = new Notification();
        $notification->title = "Payment Transfer";
        $notification->type = "transfer";
        $notification->message = "Payment of " . $request->amount . " has been successful";
        $notification->user_id = Auth::user()->id;
        $notification->icon = asset('notificationLogos/wallet.png');
        $notification->iconColor = config('notification_colors.colors.Wallet');
        $notification->save();
        $transaction = new Transaction();
        $transaction->user_id = Auth::user()->id;
        $transaction->transaction_type = "Funds Transfer";
        $transaction->amount = $request->amount;
        $transaction->transaction_date = now();
        $transaction->sign = "negative"; // Indicate outgoing funds
        $transaction->status = $status;
        $transaction->save();

        if ($responseData) {
            $transfer = new Transfer();
            $transfer->transaction_id = $transaction->id;
            $transfer->from_account_number = "1001629262";
            $transfer->to_account_number = $request->toAccount;
            $transfer->from_client_id = "149383";
            $transfer->to_client_id = $request->toClientId;
            $transfer->status = $status;
            $transfer->to_client_name = $request->toClient;
            $transfer->from_client_name = Auth::user()->email;
            $transfer->amount = $request->amount;
            $transfer->reference = $reference; // Reference for the transaction
            // if ($request->toBank == "999999") {

            //     $transfer->transfer_type = "intra";
            // } else {

            // }
            $transfer->transfer_type = $request->transferType;
            $transfer->response_message = $responseData['message'] ?? null;
            $transfer->save();
        }
    }

    private function recordIncomingFunds($userId, $amount, $reference, $beneficiaryAccount, Request $request)
    {
        // Create a new transaction for incoming funds
        $notification = new Notification();
        $notification->title = "Icoming Payments";
        $notification->type = "transfer";
        $notification->message = "Payment of " . $amount . " has been successful";
        $notification->user_id = $userId;
        $notification->icon = asset('notificationLogos/wallet.png');
        $notification->iconColor = config('notification_colors.colors.Wallet');
        $notification->save();
        $transaction = new Transaction();
        $transaction->user_id = $userId;
        $transaction->transaction_type = 'Inward Credit';
        $transaction->amount = $amount;
        $transaction->transaction_date = now();
        $transaction->sign = 'positive'; // Positive sign for incoming funds
        $transaction->status = 'Completed'; // Set status as completed or as appropriate
        $transaction->save();
        $userAccount = Auth::user()->id;
        $account = Account::where('user_id', $userAccount)->first();

        $transfer = new Transfer();
        $transfer->transaction_id = $transaction->id;
        $transfer->from_account_number = $account->account_number; // This is the originating account number
        $transfer->to_account_number = $beneficiaryAccount->account_number; // This is the beneficiary account number (same)
        $transfer->from_client_id = 'Unknown'; // Or get it from the request if available
        $transfer->reference = $reference; // Reference for the transaction
        $transfer->to_client_id = $request->toClient; // ID of the beneficiary client
        $transfer->status = 'Completed'; // Incoming funds status
        $transfer->to_client_name = $beneficiaryAccount->firstName; // Beneficiary client name
        $transfer->from_client_name = 'Unknown'; // Or get from request if available
        $transfer->amount = $amount; // Amount received
        $transfer->response_message = "Successful inward credit"; // Message for transfer
        $transfer->save();
    }

    private function generateSignature($fromAccount, $toAccount)
    {
        return hash('sha512', $fromAccount . $toAccount); // Generate SHA512 signature
    }
    public function getPoolAccountDetails()
    {
        $response = Http::withHeaders(['AccessToken' => $this->accessToken])
            ->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/account/enquiry',);
        return $response->json();
    }
    /**
     * Processes an inward credit notification received from a webhook.
     *
     * This method captures incoming webhook data related to an inward credit transaction
     * and records the transaction and transfer details in the database.
     * It does not perform validation on the incoming request data.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing
     * the webhook data, which includes information such as reference, amount, account number,
     * originator details, transaction channel, session ID, and timestamp.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success status
     * and message of the inward credit processing.
     */
    public function inwardCreditNotification(Request $request)
    {
        try {
            Log::info('Inward Credit Notification Received: ', $request->all());
            // Capture incoming webhook data directly without validation
            $reference = $request->input('reference');
            $amount = $request->input('amount');
            $accountNumber = $request->input('account_number');
            $originatorAccountNumber = $request->input('originator_account_number');
            $originatorAccountName = $request->input('originator_account_name');
            $originatorBank = $request->input('originator_bank');
            $originatorNarration = $request->input('originator_narration') ?? 'Inward Transfer';
            $transactionChannel = $request->input('transaction_channel');
            $sessionId = $request->input('session_id');
            $timestamp = $request->input('timestamp');

            $virtualAccount = VirtualAccountHistory::where('accountNumber', $accountNumber)->orderBy('created_at', 'desc')->first();
            $userId = $virtualAccount ? $virtualAccount->user_id : null;
            //if user id is not null
            if ($userId) {
                $account = Account::where('user_id', $userId)->first();
                $toclientName = $account->firstName;
            } else {
                $toclientName = null;
            }
            if ($userId !== null) {
                $transaction = new Transaction();
                $transaction->user_id = $userId;
                $transaction->transaction_type = 'Inward Credit';
                $transaction->amount = $amount;
                $transaction->transaction_date = $timestamp;
                $transaction->sign = 'positive';
                $transaction->status = 'Completed';
                $transaction->save();
                $transfer = new Transfer();
                $transfer->transaction_id = $transaction->id;
                $transfer->from_account_number = $originatorAccountNumber;
                $transfer->to_account_number = $accountNumber;
                $transfer->from_client_id = "149383";
                $transfer->to_client_id = '1234';
                $transfer->status = 'Completed';
                $transfer->to_client_name = $toclientName;
                $transfer->from_client_name = $originatorAccountName;
                $transfer->amount = $amount;
                $transfer->reference = $reference;
                $transfer->save();
                if ($virtualAccount) {
                    Log::info('Inward Credit Notification Received: for the authenticated user', $request->all());
                    $wallet = Wallet::where('user_id', $userId)->first();
                    $wallet->accountBalance = $wallet->accountBalance + $amount;
                    $wallet->totalIncome = $wallet->totalIncome + $amount;
                    $wallet->save();
                    $notification = new Notification();
                    $notification->title = "Incoming Payments";
                    $notification->type = "transfer";
                    $notification->message = "Payment of " . $amount . " has been successful";
                    $notification->user_id = $userId;
                    $notification->icon = asset('notificationLogos/wallet.png');
                    $notification->iconColor = config('notification_colors.colors.Wallet');
                    $notification->save();
                    $notificationTitle = "Incoming Payments";
                    $notificationMessage = "Incoming Funds  of " . $amount . " has been successful";
                    $notificationResponse = $this->NotificationService->sendToUserById($userId, $notificationTitle, $notificationMessage);
                    Log::info('Notification Response: ', $notificationResponse);
                    return response()->json(['status' => 'success', 'message' => 'Inward credit processed successfully.'], 200);
                } else {
                    $freeFund = new FreeFund();
                    $freeFund->amount = $amount;
                    $freeFund->accountNumber = $accountNumber;
                    $freeFund->originatorBank = $originatorBank;
                    $freeFund->originatorAccount = $originatorAccountName;
                    $freeFund->originatorAccountNumber = $originatorAccountNumber;
                    $freeFund->save();
                    return response()->json(['status' => 'success', 'message' => 'Inward credit processed successfully.'], 200);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Account not found for the provided account number.'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error processing inward credit notification: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to process inward credit notification.'], 500);
        }
    }
}
