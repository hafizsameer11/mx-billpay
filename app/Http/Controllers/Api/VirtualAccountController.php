<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VirtualAccountHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class VirtualAccountController extends Controller
{
    protected $accessToken;
    public function __construct()
    {
        $this->accessToken = config('access_token.test_token');
    }
    public function fundAccount()
    {
        $userId = Auth::user()->id;
        $apiUrl = "https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/virtualaccount";
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
        if($response->successful() && $response->json()['status'] == '00'){
            $history=new VirtualAccountHistory();
            $history->user_id=$userId;;
            $history->refference=$reference;
            $history->status="active";
            $history->accountNumber=$response->json()['accountNumber'];
            $history->save();
            return response()->json(['status'=>'success','message'=>'Account funded successfully','accountNumber'=>$response->json()['accountNumber']],200);
        }else{
            $history=new VirtualAccountHistory();
            $history->user_id=$userId;;
            $history->refference=$reference;
            $history->status="failed";
            $history->accountNumber=$response->json()['accountNumber'] ?? '000';
            $history->save();
            return response()->json(['status'=>'error','message'=>'Failed to fund account'],400);
        }

        // return response()->json($response->json());
    }
}
