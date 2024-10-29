<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
            return response()->json([
                'status' => 'success',
                'message' => 'Beneficiary details retrieved successfully',
                'data' => $response->json('data'), // Return the beneficiary details
            ], 200);
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
            'fromAccount' => 'required|string',
            'toAccount' => 'required|string',
            'amount' => 'required|numeric',
            'toBank' => 'required|string',
            'transferType' => 'required|string|in:intra,inter',
            'fromClientId' => 'required|string',
            'fromClient' => 'required|string',
            'fromSavingsId' => 'required|string', // Add this line
            'fromBvn' => 'required|string', // Add this line
            'toClientId' => 'required|string',
            'toClient' => 'required|string', // Add this line
            'toSavingsId' => 'required|string', // Add this line
            'toSession' => 'nullable|string', // Optional for inter transfers
            'toBvn' => 'nullable|string', // Optional
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 400);
        }

        // Prepare the payload
        $payload = [
            'fromAccount' => $request->fromAccount,
            'toAccount' => $request->toAccount,
            'amount' => $request->amount,
            'toBank' => $request->toBank,
            'transferType' => $request->transferType,
            'fromClientId' => $request->fromClientId,
            'fromClient' => $request->fromClient, // Add this line
            'fromSavingsId' => $request->fromSavingsId, // Add this line
            'fromBvn' => $request->fromBvn, // Add this line
            'toClientId' => $request->toClientId,
            'toClient' => $request->toClient, // Add this line
            'toSavingsId' => $request->toSavingsId, // Add this line
            'toSession' => $request->toSession, // Optional for inter transfers
            'toBvn' => $request->toBvn, // Optional
            'signature' => $this->generateSignature($request->fromAccount, $request->toAccount),
            'reference' =>  'mxPay-' . mt_rand(1000, 9999), // Generate a unique reference
        ];

        // Make the API request
        $response = Http::withHeaders([
            'AccessToken' => $this->accessToken,
        ])->post('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/transfer', $payload);

        // Check if the API request was successful
        if ($response->successful()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Transfer successful',
                'data' => $response->json('data'),
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $response->json('message'),
                'data' => $response->json(),
            ], $response->status());
        }
    }

    private function generateSignature($fromAccount, $toAccount)
    {
        return hash('sha512', $fromAccount . $toAccount); // Generate SHA512 signature
    }
    public function getPoolAccountDetails(){
        $response = Http::withHeaders(['AccessToken' => $this->accessToken])
        ->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/account/enquiry', );

        return $response->json();
    }
}
