<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class TransferApiController extends Controller
{
    public $accessToken;

    public function __construct(){
        $this->accessToken='eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiYzVmOTA4OWMtODAyMS00ZWU3LThjNjYtNTMzMjEwZjQ0NjNkIiwiaWF0IjoxNzI5OTMyMzU2LCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.uIQKrplFvnc2ta7RMpwurkoK7guwIbYMBS00NopUxGwUlpP7TC1AqhM1_hns2NEQSw6scWABoeD2PLWpBkgPsA';
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
}
