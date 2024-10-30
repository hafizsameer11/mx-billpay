<?php

namespace App\Http\Controllers;

use App\Events\AccountReleased;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransferController extends Controller
{
    public function dispatchevent(){
        event(new AccountReleased(1));
        Log::info("AccountReleased event dispatched for user 1.");
    }

}
