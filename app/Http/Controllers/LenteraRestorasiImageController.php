<?php

namespace App\Http\Controllers;

use App\Models\LenteraRestorasiImage;
use App\Models\LenteraRestorasiVision;
use App\Models\LenteraRestorasiMission;
use App\Models\LenteraRestorasiCoreValue;
use App\Models\Himpunan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LenteraRestorasiImageController extends Controller
{
    public function index()
    {
        return Inertia::render('LenteraRestorasi/Index', [
            'images' => LenteraRestorasiImage::latest()->get(),
            'visions' => LenteraRestorasiVision::latest()->get(),
            'missions' => LenteraRestorasiMission::latest()->get(),
            'coreValues' => LenteraRestorasiCoreValue::latest()->get(),
            'himpunan' => Himpunan::first()
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

        try {
            if ($request->hasFile('image')) {
                // Delete old image if it exists
                if ($image->image && Storage::disk('public')->exists($image->image)) {
                    Storage::disk('public')->delete($image->image);
                }

                $path = $request->file('image')->store('lentera-restorasi', 'public');
                $image->image = $path;
            }

            $image->title = $request->title;
            $image->save();

            return redirect()->back()->with('success', 'Image updated successfully');
        } catch (\Exception $e) {
            Log::error('Image update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update image');
        }
    }

    public function destroy(LenteraRestorasiImage $image)
    {
        try {
            // Check if image exists in storage before trying to delete
            if ($image->image && Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }

            $image->delete();

            return redirect()->back()->with('success', 'Image deleted successfully');
        } catch (\Exception $e) {
            Log::error('Image deletion error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete image');
        }
    }

    // Vision Methods
    public function storeVision(Request $request)
    {
        try {
            $request->validate([
                'vision' => 'required|string'
            ]);

            LenteraRestorasiVision::create([
                'vision' => $request->vision
            ]);

            return redirect()->back()->with('success', 'Vision added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add vision');
        }
    }

    public function updateVision(Request $request, LenteraRestorasiVision $vision)
    {
        $request->validate([
            'vision' => 'required|string'
        ]);

        try {
            $vision->update([
                'vision' => $request->vision
            ]);

            return redirect()->back()->with('success', 'Vision updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update vision');
        }
    }

    public function destroyVision(LenteraRestorasiVision $vision)
    {
        try {
            $vision->delete();
            return redirect()->back()->with('success', 'Vision deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete vision');
        }
    }

    // Mission Methods
    public function storeMission(Request $request)
    {
        $request->validate([
            'mission' => 'required|string'
        ]);

        LenteraRestorasiMission::create($request->all());
        return redirect()->back()->with('success', 'Mission added successfully');
    }

    public function updateMission(Request $request, LenteraRestorasiMission $mission)
    {
        $request->validate([
            'mission' => 'required|string'
        ]);

        $mission->update($request->all());
        return redirect()->back()->with('success', 'Mission updated successfully');
    }

    public function destroyMission(LenteraRestorasiMission $mission)
    {
        try {
            $mission->delete();
            return redirect()->back()->with('success', 'Mission deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete mission');
        }
    }

    // Core Values Methods
    public function storeCoreValue(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        LenteraRestorasiCoreValue::create($request->all());
        return redirect()->back()->with('success', 'Core Value added successfully');
    }

    public function updateCoreValue(Request $request, LenteraRestorasiCoreValue $coreValue)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        $coreValue->update($request->all());
        return redirect()->back()->with('success', 'Core Value updated successfully');
    }

    public function destroyCoreValue(LenteraRestorasiCoreValue $coreValue)
    {
        try {
            $coreValue->delete();
            return redirect()->back()->with('success', 'Core Value deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete core value');
        }
    }

    public function updateHimpunan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'yt_link' => 'nullable|string|url'
        ]);

        try {
            $himpunan = Himpunan::first();
            if (!$himpunan) {
                $himpunan = new Himpunan();
            }

            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($himpunan->logo && Storage::disk('public')->exists($himpunan->logo)) {
                    Storage::disk('public')->delete($himpunan->logo);
                }
                $path = $request->file('logo')->store('himpunan', 'public');
                $himpunan->logo = $path;
            }

            $himpunan->name = $request->name;
            $himpunan->description = $request->description;
            $himpunan->yt_link = $request->yt_link;
            $himpunan->save();

            return redirect()->back()->with('success', 'Himpunan updated successfully');
        } catch (\Exception $e) {
            Log::error('Himpunan update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update himpunan');
        }
    }
}
