<?php

namespace App\Http\Controllers;

use App\Events\AccountReleased;
use Illuminate\Http\Request;

class TransferController extends Controller
{
  public function dispatchevent(){
    event(new AccountReleased(1));
  }
}
