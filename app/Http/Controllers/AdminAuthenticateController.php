<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAuthenticateController extends Controller
{
    public function index()
    {
        return view('AdminAuthenticate.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('dashboard.index');
            } else {
                Auth::logout();
                return redirect()->back()->with('error', 'Only admins can log in.');
            }
        }

        return redirect()->back()->with('error', 'Invalid Credentials');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
