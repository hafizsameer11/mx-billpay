<?php

namespace App\Http\Controllers;

use App\Models\Smtp;
use Illuminate\Http\Request;

class SmtpController extends Controller
{
    public function index()
    {
        $smtp=Smtp::first();
        return view('smtp.index',compact('smtp'));
    }
    public function create(){
        return view('smtp.create');
    }
    public function edit(){
        $smtp=Smtp::first();
        return view('smtp.create',compact('smtp'));
    }
    public function store(Request $request){
        $smtp=new Smtp();
        $smtp->host=$request->host;
        $smtp->port=$request->port;
        $smtp->username=$request->user_name;
        $smtp->app_name=$request->app_name;
        $smtp->password=$request->password;
        $smtp->from_email=$request->from_email;
        $smtp->encryption=$request->encryption;
        $smtp->save();
        return redirect()->route('smtp.index');
    }
    public function update(Request $request){
        $smtp=Smtp::first();
        $smtp->host=$request->host;
        $smtp->port=$request->port;
        $smtp->username=$request->user_name;
        $smtp->app_name=$request->app_name;
        $smtp->password=$request->password;
        $smtp->from_email=$request->from_email;
        $smtp->encryption=$request->encryption;
        $smtp->save();
        return redirect()->route('smtp.index');
    }
}
