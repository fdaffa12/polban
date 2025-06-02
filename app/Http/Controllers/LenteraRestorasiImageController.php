<?php

namespace App\Http\Controllers;

use App\Models\LenteraRestorasiImage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class LenteraRestorasiImageController extends Controller
{
    public function index()
    {
        return Inertia::render('LenteraRestorasi/Index', [
            'images' => LenteraRestorasiImage::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|max:2048'
        ]);

        $path = $request->file('image')->store('lentera-restorasi', 'public');

        LenteraRestorasiImage::create([
            'title' => $request->title,
            'image' => $path
        ]);

        return redirect()->back()->with('success', 'Image uploaded successfully');
    }

    public function update(Request $request, LenteraRestorasiImage $image)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($image->image);
            $path = $request->file('image')->store('lentera-restorasi', 'public');
            $image->image = $path;
        }

        $image->title = $request->title;
        $image->save();

        return redirect()->back()->with('success', 'Image updated successfully');
    }

    public function destroy(LenteraRestorasiImage $image)
    {
        Storage::disk('public')->delete($image->image);
        $image->delete();

        return redirect()->back()->with('success', 'Image deleted successfully');
    }
}
