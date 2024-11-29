<?php

namespace App\Http\Controllers;

use App\Models\AccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiHandlingController extends Controller
{
    public function AccessToken(){
        $accessToken = AccessToken::all();
        return view('ApisHandle.AccessToken.AccessToken',compact('accessToken'));
    }
    
    public function addToken(){
        return view('ApisHandle.AccessToken.addAccessToken');
    }
    public function editAccessToken($id){
        $accessToken = AccessToken::find($id);
        return view('ApisHandle.AccessToken.editAccessToken',compact('accessToken'));
    }

    public function storeToken(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(),[
            'accesToken' => 'required',
            'status' => 'required'
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }


        $token = new AccessToken();
        $token->accesToken = $request->accesToken;
        $token->status = $request->status;
        $token->save();

        return redirect()->route('AccessToken')->with('success', 'Access Token Added Successfully');
        
    }

    public function updateToken(Request $request,$id){
        $validator = Validator::make($request->all(),[
            'accesToken' => 'required',
            'status' => 'required'
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $token = AccessToken::find($id);
        $token->accesToken = $request->accesToken;
        $token->status = $request->status;
        $token->save();

        return redirect()->route('AccessToken')->with('success', 'Access Token Updated Successfully');
        
    }


    public function deleteToken($id){
        $token = AccessToken::find($id);
        $token->delete();
        return redirect()->route('AccessToken')->with('success', 'Access Token Deleted Successfully');
    }

}
