<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Str;
class BillerProviderController extends Controller
{
    // set providers
    public function setProviders(){
        //get all biller items and group  by on the base of paymentitemname and store in a variable
        $billerItems = \App\Models\BillerItem::all();
        $providers = [];
        foreach ($billerItems as $item) {

            //save providers
            $provider=\App\Models\BillProviders::firstOrCreate(['title'=>$item->provider_name,'slug'=>\Illuminate\Support\Str::slug($item->provider_name)]);
            $provider->biller_category_id=$item->category_id;
            $provider->save();
            $providers[]=$provider->name;
            }

        return response()->json($providers);
    }
}
