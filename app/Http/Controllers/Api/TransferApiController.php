<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransferApiController extends Controller
{
    public $accessToken;

    public function __construct()
    {
        $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiYzVmOTA4OWMtODAyMS00ZWU3LThjNjYtNTMzMjEwZjQ0NjNkIiwiaWF0IjoxNzI5OTMyMzU2LCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.uIQKrplFvnc2ta7RMpwurkoK7guwIbYMBS00NopUxGwUlpP7TC1AqhM1_hns2NEQSw6scWABoeD2PLWpBkgPsA';
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
            $userAccount = Account::where('account_number', $accountNo)->with('user')->first();

            if ($userAccount) {
                // Account found, send additional user details
                $profilePictureUrl = asset('storage/' . $userAccount->profile_picture);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Beneficiary details retrieved successfully',
                    'data' => [
                        'beneficiaryDetails' => $response->json('data'), // Return the beneficiary details from the API
                        'firstName' => $userAccount->firstName,
                        'lastName' => $userAccount->lastName,
                        'email' => $userAccount->user->email,
                        'profilePicture' => $profilePictureUrl
                    ],
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
            return response()->json([
                'status' => 'error',
                'message' => $response->json('message'), // Error message from the API
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
        $fromAccount = "1001629262"; // This should be dynamically fetched based on the logged-in user's account
        $fromClientId = "149383";
        $fromClient = "Mx Bill Pay";
        $fromSavingsId = "162926";
        $fromBvn = "22222222226";

        // Prepare the payload for the API request
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
                // Log the exception or handle it as necessary
                Log::error('Connection error while making transfer', ['error' => $e->getMessage()]);
            }

            $attempt++;
            sleep(1); // Wait before retrying
        }

        // Check if the API request was successful
        if ($response && $response->successful()) {
            $responseData = $response->json();

            // Record the successful transaction
            //log the response of api
            Log::info('API Response:', $response->json());
            $transactionStatus = $responseData['status'] == "00" ? 'Completed' : 'Failed';
            $this->recordTransaction($request, $transactionStatus, $reference, $responseData['data']);

            // Prepare response details
            $transactionDetails = [
                'beneficiaryAccountNumber' => $request->toAccount,
                'paymentReference' => $reference,
                'clientName' => $request->toClientName,
            ];

            // Check if the beneficiary's account exists in the accounts table
            $beneficiaryAccount = Account::where('account_number', $request->toAccount)->first();

            if ($beneficiaryAccount) {
                // If it exists, fetch additional details
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transfer successful',
                    'data' => array_merge($transactionDetails, [
                        'beneficiaryFirstName' => $beneficiaryAccount->firstName,
                        'beneficiaryLastName' => $beneficiaryAccount->lastName,

                    ]),
                ], 200);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transfer successful, but beneficiary account not found in our records.',
                    'data' => $transactionDetails,
                ], 200);
            }
        } else {
            // Handle the error if the response was not successful after retries
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch the transfer response after multiple attempts.',
                'data' => $response ? $response->json() : null,
            ], 500);
        }
    }

    private function recordTransaction(Request $request, $status, $reference, $responseData = null)
    {
        $transaction = new Transaction();
        $transaction->user_id = Auth::user()->id;
        $transaction->transaction_type = "Funds Transfer";
        $transaction->amount = $request->amount;
        $transaction->transaction_date = now();
        $transaction->sign = "negative"; // Assuming negative sign for transfers
        $transaction->status = $status;
        $transaction->save();

        // Optionally save additional details in the Transfer table
        if ($responseData) {
            $transfer = new Transfer();
            $transfer->transaction_id = $transaction->id;
            $transfer->from_account_number = "1001629262"; // This should be dynamically fetched
            $transfer->to_account_number = $request->toAccount;
            $transfer->from_client_id = "149383"; // This should be dynamically fetched
            $transfer->to_client_id = $request->toClientId;
            $transfer->status = $status;
            $transfer->to_client_name = $request->toClientName;
            $transfer->from_client_name = Auth::user()->email; // Assuming the user's email
            $transfer->amount = $request->amount;
            $transfer->response_message = $responseData['message'] ?? null;
            $transfer->save();
        }
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
}
