<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AboutUsController extends Controller
{
    private function hasContentManagementAccess($user)
    {
        return in_array($user->role, ["BPH", "MEDKOM"]);
    }

    public function index()
    {
        $user = Auth::user();

        // Check if user has access to about us
        if (!$this->hasContentManagementAccess($user)) {
            abort(403, 'Unauthorized action.');
        }

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
            'au_values' => 'nullable',
            'history' => 'nullable',
        ]);

        $aboutUs = AboutUs::first() ?? new AboutUs();
        $data = $request->except(['au_image']);

        // Handle main image
        if ($request->hasFile('au_image')) {
            if ($aboutUs->au_image) {
                Storage::disk('public')->delete($aboutUs->au_image);
            }
            $data['au_image'] = $request->file('au_image')->store('about-us', 'public');
        }

        $aboutUs->fill($data);
        $aboutUs->save();

        return redirect()->back()->with('success', 'About Us updated successfully.');
    }

    public function addImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'title' => 'required|string|max:255'
        ]);

        $aboutUs = AboutUs::first();
        if (!$aboutUs) {
            return response()->json(['message' => 'About Us not found'], 404);
        }

        $path = $request->file('image')->store('about-us/gallery', 'public');

        $images = $aboutUs->au_multiple_image ?? [];
        $images[] = [
            'path' => $path,
            'title' => $request->title
        ];

        $aboutUs->au_multiple_image = $images;
        $aboutUs->save();

        return redirect()->back()->with([
            'success' => true,
            'message' => 'Image added successfully',
            'images' => $aboutUs->au_multiple_image
        ]);
    }

    public function updateImage(Request $request, $index)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);

        $aboutUs = AboutUs::first();
        if (!$aboutUs) {
            return response()->json(['message' => 'About Us not found'], 404);
        }

        $images = $aboutUs->au_multiple_image;
        if (!isset($images[$index])) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        // Update image if new file is uploaded
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($images[$index]['path']);
            $images[$index]['path'] = $request->file('image')->store('about-us/gallery', 'public');
        }

        // Update title
        $images[$index]['title'] = $request->title;

        $aboutUs->au_multiple_image = $images;
        $aboutUs->save();

        return redirect()->back()->with([
            'success' => true,
            'message' => 'Image updated successfully',
            'images' => $aboutUs->au_multiple_image
        ]);
    }

    public function removeImage(Request $request)
    {
        $request->validate([
            'index' => 'required|integer'
        ]);

        $aboutUs = AboutUs::first();
        if (!$aboutUs) {
            return response()->json(['message' => 'About Us not found'], 404);
        }

        $images = $aboutUs->au_multiple_image ?? [];
        if (!isset($images[$request->index])) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        // Remove file from storage
        Storage::disk('public')->delete($images[$request->index]['path']);

        // Remove from array and reindex
        array_splice($images, $request->index, 1);
        $aboutUs->au_multiple_image = array_values($images);
        $aboutUs->save();

        return redirect()->back()->with([
            'success' => true,
            'images' => $aboutUs->au_multiple_image
        ]);
    }
}