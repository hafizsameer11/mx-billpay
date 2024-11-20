<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TierController extends Controller
{
    //

    public function index(){
        //get only tiers title
        $tiers = Tier::select('title')->get();


        return response()->json(['status'=>'success','data'=>$tiers],200);
    }
    // single tier details
    public function show($id){
        $tier = Tier::find($id);
        return response()->json(['status'=>'success','data'=>$tier],200);
    }
    //accept request for kyc update
    public function submitRequest(Request $request){

        $userId=Auth::user()->id;
        $validator=Validator::make($request->all(),[
            'tier_id'=>'required',
        ]);
        if($validator->fails()){
            return response()->json(['status'=>'error','message'=>$validator->errors()],400);
        }
        $tier = Tier::find($request->tier_id);
    }
}
