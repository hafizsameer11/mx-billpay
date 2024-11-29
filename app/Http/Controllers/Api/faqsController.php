<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Faqcategory;
use Illuminate\Http\Request;

class faqsController extends Controller
{
    public function faqs()
    {

        $faqs = Faq::with('category')->get();
        return response()->json($faqs);
    }
    public function faqscategory()
    {

        $faqs = Faqcategory::get();
        return response()->json($faqs);
    }
}
