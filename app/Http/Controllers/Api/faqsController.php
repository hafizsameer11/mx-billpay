<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Faqcategory;
use Illuminate\Http\Request;

class faqsController extends Controller
{
    public function index()
    {

      $faqs = Faq::get();
      $faqs=$faqs->map(function($faq){
          return [
              'id' => $faq->id,
              'question' => $faq->question,
              'answer' => $faq->answer,
          ];
      });
      return response()->json(['status'=>'success','data'=>$faqs],200);
    }
    public function faqscategory()
    {

        $faqs = Faqcategory::get();
        return response()->json($faqs);
    }
}
