<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    protected $accessToken;
    public function __construct()
    {
        $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiYzVmOTA4OWMtODAyMS00ZWU3LThjNjYtNTMzMjEwZjQ0NjNkIiwiaWF0IjoxNzI5OTMyMzU2LCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.uIQKrplFvnc2ta7RMpwurkoK7guwIbYMBS00NopUxGwUlpP7TC1AqhM1_hns2NEQSw6scWABoeD2PLWpBkgPsA';
    }
    public function getTransactions()
    {
        $userId = Auth::user()->id;
    }
    public function fetchWalletTransactions(Request $request)
    {
        $request->validate([
            'accountNo' => 'required|string',
            'startDate' => 'required|date_format:Y-m-d H:i:s',
            'endDate' => 'required|date_format:Y-m-d H:i:s',
            'page' => 'nullable|integer',
            'size' => 'nullable|integer',
        ]);

        // Make the GET request to the API
        $response = Http::withHeaders([
            'AccessToken' => $this->accessToken, // Ensure this is defined
        ])->get('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/account/transactions', [
            'accountNo' => $request->accountNo,
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'transactionType' => 'wallet', // Ensure you are passing 'wallet' for wallet transactions
            'page' => $request->get('page', 0), // Default to 0 if not provided
            'size' => $request->get('size', 20), // Default to 20 if not provided
        ]);

        if ($response->successful()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Wallet transactions fetched successfully',
                'data' => $response->json(), // Return the full JSON response
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch wallet transactions',
                'data' => $response->json(), // Return the error data
            ], $response->status());
        }
    }

    public function fetchBankTransactions(Request $request)
    {
        $request->validate([
            'accountNo' => 'required|string',
            'startDate' => 'required|date_format:Y-m-d H:i:s',
            'endDate' => 'required|date_format:Y-m-d H:i:s',
            'page' => 'nullable|integer',
            'size' => 'nullable|integer',
        ]);

        $response = Http::withHeaders([
            'AccessToken' => $this->accessToken, // Ensure this is defined
        ])->get(' https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/account/transactions', [
            'accountNo' => $request->accountNo,
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'transactionType' => 'bank',
            'page' => $request->get('page', 0),
            'size' => $request->get('size', 20),
        ]);

        if ($response->successful()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Bank transactions fetched successfully',
                'data' => $response->json(),
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch bank transactions',
                'data' => $response->json(),
            ], $response->status());
        }
    }
}
