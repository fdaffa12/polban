<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class AboutUsController extends Controller
{
    public function index()
    {
        $aboutUs = AboutUs::first() ?? new AboutUs();
        return Inertia::render('AboutUs/Index', [
            'aboutUs' => $aboutUs
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'au_title' => 'required|string|max:255',
            'au_desc' => 'required|string',
            'au_image' => 'nullable|image|max:2048',
            'au_multiple_image.*' => 'nullable|image|max:2048',
            'au_values' => 'nullable',
            'history' => 'nullable',
        ]);

        $aboutUs = AboutUs::first() ?? new AboutUs();
        $data = $request->except(['au_image', 'au_multiple_image']);

        // Handle main image
        if ($request->hasFile('au_image')) {
            if ($aboutUs->au_image) {
                Storage::disk('public')->delete($aboutUs->au_image);
            }
            $data['au_image'] = $request->file('au_image')->store('about-us', 'public');
        }

        // Handle multiple images
        if ($request->hasFile('au_multiple_image')) {
            $multipleImages = [];
            foreach ($request->file('au_multiple_image') as $image) {
                $multipleImages[] = $image->store('about-us/gallery', 'public');
            }
            $data['au_multiple_image'] = $multipleImages;
        }

        $aboutUs->fill($data);
        $aboutUs->save();

        return redirect()->back()->with('success', 'About Us updated successfully.');
    }
}
