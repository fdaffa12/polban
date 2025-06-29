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
use Illuminate\Support\Facades\Auth;

class LenteraRestorasiImageController extends Controller
{
    private function hasContentManagementAccess($user)
    {
        return in_array($user->role, ["BPH", "MEDKOM"]);
    }

    public function index()
    {
        $user = Auth::user();

        // Check if user has access to lentera restorasi
        if (!$this->hasContentManagementAccess($user)) {
            abort(403, 'Unauthorized action.');
        }

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
            'image' => 'required|image|max:1024',
            'description' => 'nullable|string'
        ]);

        $path = $request->file('image')->store('lentera-restorasi', 'public');

        LenteraRestorasiImage::create([
            'title' => $request->title,
            'image' => $path,
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Image uploaded successfully');
    }

    public function update(Request $request, LenteraRestorasiImage $image)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:1024',
            'description' => 'nullable|string'
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
            $image->description = $request->description;
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
                'type' => 'required|in:caption,image',
                'vision' => $request->type === 'caption' ? 'required|string' : 'nullable',
                'image' => $request->type === 'image' ? 'required|image|max:1024' : 'nullable',
            ]);

            $data = [
                'type' => $request->type,
                'vision' => $request->type === 'image' ? 'Image Vision' : $request->vision
            ];

            if ($request->type === 'image' && $request->hasFile('image')) {
                $path = $request->file('image')->store('visions', 'public');
                $data['image'] = $path;
            }

            LenteraRestorasiVision::create($data);

            return redirect()->back()->with('success', 'Vision added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add vision');
        }
    }

    public function updateVision(Request $request, LenteraRestorasiVision $vision)
    {
        $request->validate([
            'type' => 'required|in:caption,image',
            'vision' => $request->type === 'caption' ? 'required|string' : 'nullable',
            'image' => $request->type === 'image' ? 'nullable|image|max:1024' : 'nullable',
        ]);

        try {
            $data = [
                'type' => $request->type,
                'vision' => $request->type === 'image' ? 'Image Vision' : $request->vision
            ];

            if ($request->type === 'image') {
                if ($request->hasFile('image')) {
                    if ($vision->image && Storage::disk('public')->exists($vision->image)) {
                        Storage::disk('public')->delete($vision->image);
                    }
                    $path = $request->file('image')->store('visions', 'public');
                    $data['image'] = $path;
                } else {
                    $data['image'] = $vision->image;
                }
            } else {
                $data['image'] = null;
                if ($vision->image && Storage::disk('public')->exists($vision->image)) {
                    Storage::disk('public')->delete($vision->image);
                }
            }

            $vision->update($data);

            return redirect()->back()->with('success', 'Vision updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update vision');
        }
    }

    public function destroyVision(LenteraRestorasiVision $vision)
    {
        try {
            if ($vision->type === 'image' && $vision->image) {
                Storage::disk('public')->delete($vision->image);
            }
            $vision->delete();
            return redirect()->back()->with('success', 'Vision deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete vision');
        }
    }

    // Mission Methods
    public function storeMission(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:caption,image',
                'mission' => $request->type === 'caption' ? 'required|string' : 'nullable',
                'image' => $request->type === 'image' ? 'required|image|max:1024' : 'nullable',
            ]);

            $data = [
                'type' => $request->type,
                'mission' => $request->type === 'image' ? 'Image Mission' : $request->mission
            ];

            if ($request->type === 'image' && $request->hasFile('image')) {
                $path = $request->file('image')->store('missions', 'public');
                $data['image'] = $path;
            }

            LenteraRestorasiMission::create($data);

            return redirect()->back()->with('success', 'Mission added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add mission');
        }
    }

    public function updateMission(Request $request, LenteraRestorasiMission $mission)
    {
        $request->validate([
            'type' => 'required|in:caption,image',
            'mission' => $request->type === 'caption' ? 'required|string' : 'nullable',
            'image' => $request->type === 'image' ? 'nullable|image|max:1024' : 'nullable',
        ]);

        try {
            $data = [
                'type' => $request->type,
                'mission' => $request->type === 'image' ? 'Image Mission' : $request->mission
            ];

            if ($request->type === 'image') {
                if ($request->hasFile('image')) {
                    if ($mission->image && Storage::disk('public')->exists($mission->image)) {
                        Storage::disk('public')->delete($mission->image);
                    }
                    $path = $request->file('image')->store('missions', 'public');
                    $data['image'] = $path;
                } else {
                    $data['image'] = $mission->image;
                }
            } else {
                $data['image'] = null;
                if ($mission->image && Storage::disk('public')->exists($mission->image)) {
                    Storage::disk('public')->delete($mission->image);
                }
            }

            $mission->update($data);

            return redirect()->back()->with('success', 'Mission updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update mission');
        }
    }

    public function destroyMission(LenteraRestorasiMission $mission)
    {
        try {
            if ($mission->type === 'image' && $mission->image) {
                Storage::disk('public')->delete($mission->image);
            }
            $mission->delete();
            return redirect()->back()->with('success', 'Mission deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete mission');
        }
    }

    // Core Values Methods
    public function storeCoreValue(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:caption,image',
                'title' => 'required|string|max:255',
                'description' => $request->type === 'caption' ? 'required|string' : 'nullable',
                'image' => $request->type === 'image' ? 'required|image|max:1024' : 'nullable',
            ]);

            $data = [
                'type' => $request->type,
                'title' => $request->title,
                'description' => $request->type === 'image' ? 'Image Core Value' : $request->description
            ];

            if ($request->type === 'image' && $request->hasFile('image')) {
                $path = $request->file('image')->store('core-values', 'public');
                $data['image'] = $path;
            }

            LenteraRestorasiCoreValue::create($data);

            return redirect()->back()->with('success', 'Core Value added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add core value');
        }
    }

    public function updateCoreValue(Request $request, LenteraRestorasiCoreValue $coreValue)
    {
        $request->validate([
            'type' => 'required|in:caption,image',
            'title' => 'required|string|max:255',
            'description' => $request->type === 'caption' ? 'required|string' : 'nullable',
            'image' => $request->type === 'image' ? 'nullable|image|max:1024' : 'nullable',
        ]);

        try {
            $data = [
                'type' => $request->type,
                'title' => $request->title,
                'description' => $request->type === 'image' ? 'Image Core Value' : $request->description
            ];

            if ($request->type === 'image') {
                if ($request->hasFile('image')) {
                    if ($coreValue->image && Storage::disk('public')->exists($coreValue->image)) {
                        Storage::disk('public')->delete($coreValue->image);
                    }
                    $path = $request->file('image')->store('core-values', 'public');
                    $data['image'] = $path;
                } else {
                    $data['image'] = $coreValue->image;
                }
            } else {
                $data['image'] = null;
                if ($coreValue->image && Storage::disk('public')->exists($coreValue->image)) {
                    Storage::disk('public')->delete($coreValue->image);
                }
            }

            $coreValue->update($data);

            return redirect()->back()->with('success', 'Core Value updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update core value');
        }
    }

    public function destroyCoreValue(LenteraRestorasiCoreValue $coreValue)
    {
        try {
            if ($coreValue->type === 'image' && $coreValue->image) {
                Storage::disk('public')->delete($coreValue->image);
            }
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
            'logo' => 'nullable|image|max:1024',
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
