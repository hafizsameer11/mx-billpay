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
        $providers = BillProviders::where('biller_category_id', $id)->first();
        $caat=BillerCategory::select('category','id')->where('id',$providers->biller_category_id)->first();
        $category=$caat->category;
       $provider=[
        'id'=>$providers->id,
        'title'=>$providers->title,
        'slug'=>$providers->slug,
        'logo'=>asset($providers->logo),
        'category'=>$category,
        'category_id'=>$caat->id
       ];

        return response()->json(['status'=>'success','data'=>$provider],200);
    }

}
