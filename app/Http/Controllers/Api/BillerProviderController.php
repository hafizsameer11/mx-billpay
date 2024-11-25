<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillerCategory;
use App\Models\BillProviders;
use Illuminate\Http\Request;
// use Str;
class BillerProviderController extends Controller
{
    // set providers
    public function setProviders()
    {
        $billerItems = \App\Models\BillerItem::all()->groupBy('provider_name');
        $providers = [];

        foreach ($billerItems as $providerName => $items) {
            foreach ($items->groupBy('category_id') as $categoryId => $groupedItems) {
                $provider = BillProviders::firstOrCreate(
                    [
                        'title' => $providerName,
                        'biller_category_id' => $categoryId,
                    ],
                    [
                        'slug' => \Illuminate\Support\Str::slug($providerName . '-' . $categoryId),
                    ]
                );

                $providers[] = $provider->title;
            }
        }

        return response()->json($providers);
    }

    public function getProviders($id)
    {
        $providers = BillProviders::where('biller_category_id', $id)->get();
        $categoryData = BillerCategory::select('category', 'id')->where('id', $id)->first();

        // Ensure category data exists
        if (!$categoryData) {
            return response()->json(['status' => 'error', 'message' => 'Category not found'], 404);
        }

        $category = $categoryData->category;
        $providerList = [];

        foreach ($providers as $provider) {
            $providerList[] = [
                'id' => $provider->id,
                'title' => $provider->title,
                'slug' => $provider->slug,
                'logo' => asset($provider->logo),
                'category' => $category,
                'category_id' => $categoryData->id,
            ];
        }

        return response()->json(['status' => 'success', 'data' => $providerList], 200);
    }


}
