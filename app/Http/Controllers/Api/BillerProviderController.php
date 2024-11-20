<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Str;
class BillerProviderController extends Controller
{
    // set providers
    public function setProviders()
    {
        // Fetch all biller items and group by provider_name
        $billerItems = \App\Models\BillerItem::all()->groupBy('provider_name');
        $providers = [];

        foreach ($billerItems as $providerName => $items) {
            // Create or fetch the provider once for each unique provider_name
            $provider = \App\Models\BillProviders::firstOrCreate(
                [
                    'title' => $providerName,
                ],
                [
                    'slug' => \Illuminate\Support\Str::slug($providerName),
                    'biller_category_id' => $items->first()->category_id,
                ]
            );

            $providers[] = $provider->title;
        }

        return response()->json($providers);
    }

}
