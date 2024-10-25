<?php

namespace App\Http\Controllers;

use App\Jobs\FetchBankListJob;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public $accessToken;
    public function __construct()
    {
        $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiNjJmOTFlYTItMTQ4NC00MTY1LTg0N2MtN2QxZmI1NzZlYmI3IiwiaWF0IjoxNzI5NTExNzI4LCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.pof77yeMdkhZyO8fQtaFmwT-3bq8fawbkcduxteAfGzP0U9HzaI-vdnrUok90oPvQ_PscdPD1vUPP4Ya5byITA';
    }
    public function index(){
        $accessToken = $this->accessToken; // Get the access token
        FetchBankListJob::dispatch($accessToken);
        return response()->json(['status'=>'success','message'=>'Bank List is being fetched'], 200);
    }
}
