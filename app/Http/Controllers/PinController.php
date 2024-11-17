<?php

namespace App\Http\Controllers;

use App\Models\Pin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PinController extends Controller
{
    // for api
    public function setPin(Request $request){
        $userId=Auth::user()->id;
        $validate=Validator::make($request->all(),[
            'pin'=>'required|string'
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()],400);
        }
       $pin=new Pin();
       $pin->pin=$request->pin;
       $pin->user_id=$userId;
       $pin->save();
       if($pin){
        return response()->json(['status'=>'success','message'=>'Pin set successfully'],200);
       }else{
        return response()->json(['status'=>'error','message'=>'Failed to set pin'],500);
       }
    }
    public function changePin(Request $request){
        $userId=Auth::user()->id;
        $validate=Validator::make($request->all(),[
            'oldPin'=>'required|string',
            'newPin'=>'required|string'
            ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()],400);
        }
        $pin=Pin::where('userId',$userId)->first();
        if($pin->pin!=$request->oldPin){
            return response()->json(['status'=>'error','message'=>'Old pin is incorrect'],500);
        }
        $pin->pin=$request->newPin;
        $pin->save();
        if($pin){
            return response()->json(['status'=>'success','message'=>'Pin changed successfully'],200);
           }else{
            return response()->json(['status'=>'error','message'=>'Failed to change pin'],500);
           }
    }
    public function checkPin(Request $request){
        $userId=Auth::user()->id;
        $validate=Validator::make($request->all(),[
            'pin'=>'required|string'
            ]);
            if($validate->fails()){
                return response()->json(['status'=>'error','message'=>$validate->errors()],400);
                }
        $pin=Pin::where('userId',$userId)->first();
        if($pin->pin==$request->pin){
            return response()->json(['status'=>'success','message'=>'Pin is correct'],200);
            }else{
                return response()->json(['status'=>'error','message'=>'Pin is incorrect'],500);
                }
    }
}
