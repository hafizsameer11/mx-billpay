<?php

namespace App\Http\Controllers;

use App\Jobs\FetchBillerCategories;
use App\Models\BillerCategory;
use Illuminate\Http\Request;

class BillerCategoryController extends Controller
{
    public function fetchCategories(Request $request)
    {
        FetchBillerCategories::dispatch();
        return response()->json(['message' => 'Fetching biller categories in progress.']);
    }

    public function showCategories()
    {
        $categories = BillerCategory::all();
        return response()->json($categories);
    }
}
