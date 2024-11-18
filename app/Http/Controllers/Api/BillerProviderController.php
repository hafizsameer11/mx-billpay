<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BillerProviderController extends Controller
{
    // set providers
    public function setProviders(){
        //get all biller items and group  by on the base of paymentitemname and store in a variable
        $billerItems = \App\Models\BillerItem::all();
        $providers = [];
        foreach ($billerItems as $item) {
            $providers[$item->paymentitemname][] = $item;
            }

        return response()->json($providers);
    }
}
