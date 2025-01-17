<?php

namespace App\Http\Controllers;

use App\Jobs\FetchBillerCategories;
use App\Jobs\FetchBillerItems;

use App\Models\BillerCategory;
use App\Models\BillerItem;
use App\Models\BillProviders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BillerCategoryController extends Controller
{
    protected $accessToken;

    public function __construct()
    {
        $this->accessToken = config('access_token.live_token');
        // $this->accessToken = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4MTUiLCJ0b2tlbklkIjoiZGE1YjM5ZDItMGE2MS00MGE5LTg2ZGYtNTFjNDE5NmU4MmMyIiwiaWF0IjoxNzMxOTIyNjMyLCJleHAiOjkyMjMzNzIwMzY4NTQ3NzV9.D8lFZCna6PZNIXnmJt-Xwc2JJ9rYxNPv4x5yDwRnldGs6tZu8KAlCoXumVIcXuUrOvcEud0hSIkQ7hZUjsFh7Q'; // Replace with your access token logic
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
        $billerCategories = BillerCategory::all();
        return view('billpayment.index', compact('billerItems', 'billerCategories'));
    }
    public function bulkAddCommission(Request $request)
    {
        $request->validate([
            'bulk_fixed_commission' => 'nullable|numeric|min:0',
            'bulk_percentage_commission' => 'nullable|numeric|min:0|max:100',
        ]);
        $biller_category_id = $request->biller_category ?? null;
        $fixedCommission = $request->bulk_fixed_commission ?? 0;
        $percentageCommission = $request->bulk_percentage_commission ?? 0;
        if ($biller_category_id) {
            $billerCategory = BillerCategory::find($biller_category_id);
            $billerCategory->fixed_commission = $fixedCommission;
            $billerCategory->percentage_commission = $percentageCommission;
            $billerCategory->save();

            return response()->json(['success' => true, 'message' => 'Bulk commission added successfully. By using category filter.']);
        }

        BillerCategory::query()->update(['fixed_commission' => $fixedCommission, 'percentage_commission' => $percentageCommission]);

        return response()->json(['success' => true, 'message' => 'Bulk commission added successfully.']);
    }

    public function editBillerItemTitle(Request $request)
    {
        $itemId = $request->item_id;
        $itemTitle = $request->item_title;
        $item = BillerItem::find($itemId);
        $item->paymentitemname = $itemTitle;
        $item->save();
        return redirect()->back()->with('success', 'Item title saved successfully!');
    }
    public function changeItemStatus($id)
    {
        $item = BillerItem::find($id);
        $item->status = !$item->status;
        $item->save();
        return redirect()->back()->with('success', 'Item status changed successfully!');
    }
    public function chnageProviderStatus($id)
    {
        // $item = BillerItem::find($id);
        $provider = BillProviders::find($id);
        $provider->status = !$provider->status;
        $provider->save();
        return redirect()->back()->with('success', 'Provider status changed successfully!');
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


    public function storeOrUpdateCategoryTitle(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'category_title' => 'required|string|max:255',
        ]);

        $category = BillerCategory::find($request->id);
        $category->category_title = $request->category_title;
        $category->save();

        return redirect()->back()->with('success', 'Category title saved successfully!');
    }
    public function storeOrUpdateDescription(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'category_description' => 'required|string|max:255',
        ]);

        $category = BillerCategory::find($request->id);
        $category->category_description = $request->category_description;
        $category->save();

        return redirect()->back()->with('success', 'Category title saved successfully!');
    }
    public function storeOrUpdateSelectTitle(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'select_title' => 'required|string|max:255',
        ]);

        $category = BillerCategory::find($request->id);
        $category->select_title = $request->select_title;
        $category->save();

        return redirect()->back()->with('success', 'Select title saved successfully!');
    }
    public function storeOrUpdateProvider(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'provider_title' => 'required|string|max:255',
        ]);

        $category = BillProviders::find($request->id);
        $category->provider_title = $request->provider_title;
        $category->save();

        return redirect()->back()->with('success', 'Provider title saved successfully!');
    }
    public function storeOrUpdateProviderDescription(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'provider_description' => 'required|string|max:255',
        ]);

        $category = BillProviders::find($request->id);
        $category->provider_description = $request->provider_description;
        $category->save();

        return redirect()->back()->with('success', 'Provider title saved successfully!');
    }


    public function storeOrUpdateProviderSelectTitle(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'select_title' => 'required|string|max:255',
        ]);

        $category = BillProviders::find($request->id);
        $category->select_title = $request->select_title;
        $category->save();

        return redirect()->back()->with('success', 'Select title saved successfully!');
    }

    public function bulkcomissionByVFD(Request $request)
    {
        $request->validate([
            'bulk_fixed_commission' => 'nullable|numeric',
            'bulk_percentage_commission' => 'nullable|numeric|min:-100|max:100', // Allowing negative percentage values
        ]);

        $biller_provider_id = $request->biller_category ?? null;
        $fixedCommission = $request->bulk_fixed_commission ?? 0;
        $percentageCommission = $request->bulk_percentage_commission ?? 0;

        if ($biller_provider_id) {
            $billerCategory = BillProviders::find($biller_provider_id);
            $billerCategory->fixed_comission = $fixedCommission;
            $billerCategory->percentage_comission = $percentageCommission;
            $billerCategory->save();

            return redirect()->back()->with('success', 'Bulk commission added successfully. By using category filter.');
        }

        BillProviders::query()->update(['fixed_comission' => $fixedCommission, 'percentage_comission' => $percentageCommission]);

        return redirect()->back()->with('success', 'Bulk commission added successfully. By using category filter.');
    }

}
