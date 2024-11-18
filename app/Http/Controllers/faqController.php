<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Faqcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class faqController extends Controller
{

    public function index()
    {
        $faqCategories = Faqcategory::all();
        return view('Faq.category', compact('faqCategories'));
    }
    public function category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|unique:faqcategories,category_name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $faqCategory = new Faqcategory();
        $faqCategory->category_name = $request->category_name;
        $faqCategory->save();

        return redirect()->back()->with('success', 'Category added successfully');
    }

    public function categoryEdit($id)
    {

        $faqCategory = Faqcategory::find($id);
        return view('Faq.categoryedit', compact('faqCategory'));
    }
    public function categoryupdate($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|unique:faqcategories,category_name,' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $faqCategory = Faqcategory::find($id);
        $faqCategory->category_name = $request->category_name;
        $faqCategory->save();

        return redirect()->back()->with('success', 'Category updated successfully');
    }

    public function categoryDelete($id)
    {

        $faqCategory = Faqcategory::find($id);
        $faqCategory->delete();
        return redirect()->back()->with('success', 'Category deleted successfully');
    }

    public function  addFaqs()
    {
        $categories = Faqcategory::all();
        return view('Faq.addfaq', compact('categories'));
    }

    public function storeFaqs(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'question' => 'required',
            'answer' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $faq = new Faq();
        $faq->category_id = $request->category_id;
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();

        return redirect()->back()->with('success', 'Faq added successfully');
    }


    public function editFaqs($id, Request $request)
    {
        $faq = Faq::find($id);
        $categories = Faqcategory::all();
        return view('Faq.editfaq', compact('faq', 'categories'));
    }

    public function updateFaq(Request $request, $id)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'question' => 'required',
            'answer' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $faq = Faq::findOrFail($id);

        $faq->category_id = $request->category_id;
        $faq->question = $request->question;
        $faq->answer = $request->answer;

        $faq->save();

        return redirect()->route('faq.show')->with('success', 'Faq updated successfully');
    }

    public function deleteFaqs($id)
    {
        $faq = Faq::find($id);
        $faq->delete();
        return redirect()->back()->with('error', 'Faq deleted successfully');
    }
}
