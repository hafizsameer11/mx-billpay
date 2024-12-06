<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;

class SlideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $slides = Slide::all(); // Fetch all slides
        return view('slides.index', compact('slides'));
    }

    public function create()
    {
        return view('slides.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image upload
        ]);

        $imagePath = $request->file('image')->store('slides', 'public'); // Store image

        Slide::create([
            'image' => $imagePath,
        ]);

        return redirect()->route('slides.index')->with('success', 'Slide created successfully!');
    }

    public function show(Slide $slide)
    {
        return view('slides.show', compact('slide'));
    }

    public function edit(Slide $slide)
    {
        return view('slides.edit', compact('slide'));
    }

    public function update(Request $request, Slide $slide)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('slides', 'public');
            $slide->update(['image' => $imagePath]);
        }

        return redirect()->route('slides.index')->with('success', 'Slide updated successfully!');
    }

    public function destroy(Slide $slide)
    {
        $slide->delete();
        return redirect()->route('slides.index')->with('success', 'Slide deleted successfully!');
    }
}
