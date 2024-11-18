<?php

namespace App\Http\Controllers;

use App\Jobs\FetchBillerCategories;
use App\Jobs\FetchBillerItems;

use App\Models\BillerCategory;
use App\Models\BillerItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BillerCategoryController extends Controller
{
    protected $accessToken;

    public function __construct()
    {
        $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiIzNjgiLCJ0b2tlbklkIjoiMjdiOGNmZjEtYjExNS00MGMzLWIwNDAtMDVjNmViMjljNTk3IiwiaWF0IjoxNzMxOTExNDIyLCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.T4dMWHEwST4OUskaQzl1W-dS1rDhFpi7vgPsCfEM58itWKDnbYabnXJnfis4Vr7mIDobzrCaA-UKq9oVjdo49g'; // Replace with your access token logic
    }

    public function fetchCategories(Request $request)
    {
        FetchBillerCategories::dispatch();
        return redirect()->back()->with('success', 'Categories are being fetched');
    }

    public function showCategories()
    {
        $categories = BillerCategory::all();
        return response()->json($categories);
    }
    public function index()
    {
        $categories = BillerCategory::all();
        // return response()->json($categories);
        return view('billpayment.category', compact('categories'));
    }
    public function fetchBillerItemsForCategory($categoryName)
    {
        $billercategory = BillerCategory::where('category', $categoryName)->first();

        if ($billercategory) {
            FetchBillerItems::dispatch($categoryName, $billercategory->id);
            return redirect()->back()->with('success', 'Biller items are being fetched for category: ' . $categoryName);
        } else {
            return redirect()->back()->with('error', 'Category not found!');
        }
    }

    public function showBillerItems()
    {
        $billerItems = BillerItem::with('category')->paginate(10);
        return view('billpayment.index', compact('billerItems'));
    }
    public function bulkAddCommission(Request $request)
    {
        $request->validate([
            'bulk_fixed_commission' => 'nullable|numeric|min:0',
            'bulk_percentage_commission' => 'nullable|numeric|min:0|max:100',
        ]);
        $fixedCommission = $request->bulk_fixed_commission ?? 0;
        $percentageCommission = $request->bulk_percentage_commission ?? 0;
        BillerItem::query()->update([
            'fixed_commission' => $fixedCommission,
            'percentage_commission' => $percentageCommission,
        ]);

        return response()->json(['success' => true, 'message' => 'Bulk commission added successfully.']);
    }
    public function addCommission(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:biller_items,id',
            'fixed_commission' => 'nullable|numeric|min:0',
            'percentage_commission' => 'nullable|numeric|min:0|max:100',
        ]);
        $item = BillerItem::find($request->item_id);
        $item->fixed_commission = $request->fixed_commission ?? 0;
        $item->percentage_commission = $request->percentage_commission ?? 0;
        $item->save();

        return response()->json(['success' => true, 'message' => 'Commission added successfully.']);
    }
}
