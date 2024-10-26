<?php

namespace App\Http\Controllers;

use App\Jobs\FetchBankListJob;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public $accessToken;
    public function __construct()
    {
        $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiYzVmOTA4OWMtODAyMS00ZWU3LThjNjYtNTMzMjEwZjQ0NjNkIiwiaWF0IjoxNzI5OTMyMzU2LCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.uIQKrplFvnc2ta7RMpwurkoK7guwIbYMBS00NopUxGwUlpP7TC1AqhM1_hns2NEQSw6scWABoeD2PLWpBkgPsA';
    }
    public function index(){
        $accessToken = $this->accessToken; // Get the access token
        FetchBankListJob::dispatch($accessToken);
        return response()->json(['status'=>'success','message'=>'Bank List is being fetched'], 200);
    }
}
