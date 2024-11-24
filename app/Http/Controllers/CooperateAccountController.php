<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CooperateAccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CooperateAccountController extends Controller
{
    protected $accessToken;
    public function __construct(){
        $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiZGE1YjM5ZDItMGE2MS00MGE5LTg2ZGYtNTFjNDE5NmU4MmMyIiwiaWF0IjoxNzMxOTIyNjMyLCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.D8lFZCna6PZNIXnmJt-Xwc2JJ9rYxNPv4x5yDwRnldGs6tZu8KAlCoXumVIcXuUrOvcEud0hSIkQ7hZUjsFh7Q';
    }
    //approve cooperate account request and hit api for checking
    public function index($id){
    $requestId=$id;
    $request=CooperateAccountRequest::where('id',$requestId)->first();
    $request->status="approved";
    $request->save();
    if($request){
        $account=Account::where('user_id',$request->user_id)->first();
        $account->status="PND";
        $account->save();
        if($account){
            $rcNumber=$request->rcNumber;
            $companyName=$request->companyName;
            $incorporationDate=$request->incorporationDate;
            $bvn=$account->bvn;
             $accessToken = $this->accessToken;
        $response = Http::withHeaders(['AccessToken' => $accessToken])
            ->timeout(300)
            ->post('https://api-devapps.vfdbank.systems/vtech-wallet/api/v1.1/wallet2/client/corporate', [
                'rcNumber' => $rcNumber,
                'companyName' => $companyName,
                'incorporationDate' => $incorporationDate,
                'bvn' => $bvn,
            ]);
            if($response->successful()){
                $responseData=$response->json();
                if($responseData['status']=='00'){
                    $account->account_number=$responseData['data']['accountNo'];
                    $account->save();
                    return response()->json(['status'=>'success','message'=>$responseData['message']],200);
                }else{
                    return response()->json(['status'=>'error','message'=>$responseData['message']],400);
                }

            }else{

            }
        }else{

        }
    }else{

    }


    }
}
