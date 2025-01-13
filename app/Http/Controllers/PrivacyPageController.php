<?php

namespace App\Http\Controllers;

use App\Models\PrivacyPageLink;
use Illuminate\Http\Request;

class PrivacyPageController extends Controller
{
    public function index()
    {
        // Fetch the single link
        $link = PrivacyPageLink::first();
        return view('privacy-link', compact('link'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'link' => 'required|url',
        ]);

        // Fetch the existing link or create a new instance
        $privacyLink = PrivacyPageLink::firstOrNew([]);
        $privacyLink->link = $request->link;
        $privacyLink->save();

        return redirect()->route('privacy-link.index')->with('success', 'Privacy link saved successfully!');
    }
    public function apiUrl()
    {
        $link = PrivacyPageLink::first();
        return response()->json([
            'status' => 'success',
            'message' => 'Privacy link saved successfully!',
            'data' => $link->link,
        ], 200);
    }
}
