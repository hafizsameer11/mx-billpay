<?php

namespace App\Http\Controllers;

use App\Models\SocialMediaLinks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SocialMediaController extends Controller
{
    public function index()
    {
        $socialMedia = SocialMediaLinks::all();
        return view('SocailMedia.index',compact('socialMedia'));
    }
    public function create()
    {
        return view('SocailMedia.create');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'link' => 'required',
            'icon' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $fileName = '';
        if ($request->hasFile('icon')) {
            $fileName = time() . '.' . $request->icon->extension();

             $request->icon->move(public_path('images/socialMedia'), $fileName);
             $filePath = "/images/socialMedia/" . $fileName;

            $socialMedia = new SocialMediaLinks();
            $socialMedia->title = $request->title;
            $socialMedia->link = $request->link;
            $socialMedia->icon = $filePath;
            $socialMedia->save();
    
        }

        else{
            return redirect()->back()->with('error', 'Image upload failed.');

        }
        return redirect()->route('social.media.index')->with('success','Socail media added successfully');


    }

    public function edit($id){
        $item = SocialMediaLinks::find($id);
        return view('SocailMedia.edit',compact('item'));
    }

    public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required',
        'link' => 'required',
        'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Optional image validation
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator->errors())->withInput();
    }

    $socialMedia = SocialMediaLinks::find($id);
    $socialMedia->title = $request->title;
    $socialMedia->link = $request->link;

    // Check if a new icon is uploaded
    if ($request->hasFile('icon')) {
        // Delete the old icon file if exists
        if ($socialMedia->icon && file_exists(public_path($socialMedia->icon))) {
            unlink(public_path($socialMedia->icon)); // Remove old file
        }

        // Upload the new icon
        $fileName = time() . '.' . $request->icon->extension();
        $request->icon->move(public_path('assets/images/socialMedia'), $fileName);
        $socialMedia->icon = '/assets/images/socialMedia/' . $fileName;
    }

    $socialMedia->save(); // Save the updated data

    return redirect()->route('social.media.index')->with('success', 'Social media updated successfully.');
}

public function delete($id){
    $item = SocialMediaLinks::find($id);
    $item->delete();
    return redirect()->route('social.media.index')->with('success','Social media deleted successfully');
}

}
