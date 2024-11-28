<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VirtualAccountHistory;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class VirtualAccountController extends Controller
{
    protected $accessToken;
    public function __construct()
    {
        $this->accessToken = config('access_token.live_token');
        // $this->accessToken = config('access_token.test_token');
    }
    public function fundAccount() {
        $userId = Auth::user()->id;
        $virtualAccount = VirtualAccountHistory::where('user_id', $userId)
        ->orderBy('created_at', 'desc') // Order by created_at in descending order
        ->first();

        // return response()->json([
        //     'date'=>Carbon::now(),
        //    'expiryDate' => $virtualAccount->expiryDate->toIso8601String(),
        // ]);
        if ($virtualAccount && $virtualAccount->expiryDate->greaterThan(Carbon::now())) {
            return response()->json([
                'status' => 'success',
                'message' => 'Account Already Valid',
                'data' => [
                    'accountNumber' => $virtualAccount->accountNumber,
                    'expiryDate' => $virtualAccount->expiryDate->toIso8601String(),
                ]
            ], 200);
        }

        $apiUrl = "https://api-apps.vfdbank.systems/vtech-wallet/api/v1/wallet2/virtualaccount";
        $reference = 'mxBillPay-' . mt_rand(1000, 99999);
        $payload = [
            'amount' => '1000',
            'merchantName' => 'Mx Bill Pay',
            'merchantId' => '149383',
            'reference' => $reference,
            'amountValidation' => 'A5'
        ];

        $response = Http::withHeaders([
            'AccessToken' => $this->accessToken,
        ])->post($apiUrl, $payload);

        if ($response->successful() && $response->json()['status'] == '00') {
            $history = new VirtualAccountHistory();
            $history->user_id = $userId;
            $history->refference = $reference;
            $history->status = "active";
            $history->accountNumber = $response->json()['accountNumber'];
            $history->expiryDate = Carbon::createFromTimestamp( Carbon::now()->addMinutes(4320)->timestamp)->toDateTimeString(); // Store as timestamp
            $history->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Account funded successfully',
                'data' => [
                    'accountNumber' => $response->json()['accountNumber'],
                    'expiryDate' => Carbon::createFromTimestamp( Carbon::now()->addMinutes(4320)->timestamp)->toDateTimeString(),
                ]
            ], 200);
        } else {
            $history = new VirtualAccountHistory();
            $history->user_id = $userId;
            $history->refference = $reference;
            $history->status = "failed";
            $history->accountNumber = $response->json()['accountNumber'] ?? '000';
            $history->save();

            return response()->json(['status' => 'error', 'message' => 'Failed to fund account'], 400);
        }
    }
    public function balance(){
        $userId = Auth::user()->id;
        $wallet=Wallet::where('user_id', $userId)->first();
        return response()->json([
            'status'=>'success',
            'balance'=>$wallet->accountBalance
        ]);
    }

}
