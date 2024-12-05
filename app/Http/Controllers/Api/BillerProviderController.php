<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillerCategory;
use App\Models\BillProviders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
                'providerTitle'=>$provider->provider_title,
                'description' => $provider->provider_description,
                'selectTitle' => $provider->select_title,
                'slug' => $provider->slug,
                'logo' => asset($provider->logo),
                'category' => $category,
                'category_id' => $categoryData->id,
            ];
        }

        return response()->json(['status' => 'success', 'data' => $providerList], 200);
    }
    public function index(Request $request){
        $title = $request->input('title');
        $serviceProviders = BillProviders::with('category')->
    when($title, function ($query) use ($title) {
            $query->where(function ($query) use ($title) {
                $query->where('title', 'like', '%' . $title . '%'); // Searching in account's firstName
            });
        })->paginate(10);
        return view('billpayment.serviceprovider',compact('serviceProviders'));
    }

    public function logoStore(Request $request)
{
    $validator = Validator::make($request->all(), [
        'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'item_id' => 'required|exists:bill_providers,id',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator->errors())->withInput();
    }

    $serviceProvider = BillProviders::find($request->item_id);

    if ($request->hasFile('logo')) {
        $file = $request->file('logo');
        $logoPath = '/images/serviceProvider/' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('/images/serviceProvider/'), $logoPath);

        // Assign the file path to the model
        $serviceProvider->logo = $logoPath;
    }

    $serviceProvider->save();

    return redirect()->back()->with('success', 'Logo updated successfully');
}


}
