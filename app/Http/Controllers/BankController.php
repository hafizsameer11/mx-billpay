<?php

namespace App\Http\Controllers;

use App\Jobs\FetchBankListJob;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public $accessToken;
    public function __construct()
    {
        $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiZGE1YjM5ZDItMGE2MS00MGE5LTg2ZGYtNTFjNDE5NmU4MmMyIiwiaWF0IjoxNzMxOTIyNjMyLCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.D8lFZCna6PZNIXnmJt-Xwc2JJ9rYxNPv4x5yDwRnldGs6tZu8KAlCoXumVIcXuUrOvcEud0hSIkQ7hZUjsFh7Q';
    }
    public function index(){
        $accessToken = $this->accessToken; // Get the access token
        FetchBankListJob::dispatch($accessToken);
        return response()->json(['status'=>'success','message'=>'Bank List is being fetched'], 200);
    }
}
