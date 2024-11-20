<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillProviders;
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
    public function getProviders($id)
    {
        $providers = BillProviders::where('biller_category_id', $id)->get();

        $provde = [];
        foreach ($providers as $provider) {
            if (isset($provider->id) && isset($provider->title) && isset($provider->logo)) {
                $provde[] = [
                    'id' => $provider->id,
                    'title' => $provider->title,
                    'logo' => $provider->logo,
                ];
            }
        }

        return response()->json($provde);
    }

}
