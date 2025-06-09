<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventDate;
use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        return Inertia::render('Events/Index', [
            'events' => Event::with(['department', 'dates'])->latest()->get(),
            'departments' => Department::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'event_name' => 'required|string|max:255',
            'event_detail' => 'required|string',
            'fee_type' => 'required|in:free,paid',
            'fee_amount' => 'required_if:fee_type,paid|nullable|numeric|min:0',
            'event_flyer' => 'required|image|max:2048',
            'event_gallery.*' => 'nullable|image|max:2048',
            'event_doc' => 'nullable|mimes:pdf,doc,docx|max:10240',
            'status' => 'required|in:coming_soon,running,closed',
            'dates' => 'required|array|min:1',
            'dates.*.event_date' => 'required|date',
            'dates.*.event_time' => 'required'
        ]);

        try {
            $flyerPath = $request->file('event_flyer')->store('events/flyers', 'public');

            $galleryPaths = [];
            if ($request->hasFile('event_gallery')) {
                foreach ($request->file('event_gallery') as $image) {
                    $galleryPaths[] = $image->store('events/gallery', 'public');
                }
            }

            $docPath = null;
            if ($request->hasFile('event_doc')) {
                $docPath = $request->file('event_doc')->store('events/docs', 'public');
            }

            $event = Event::create([
                'department_id' => $request->department_id,
                'event_name' => $request->event_name,
                'event_detail' => $request->event_detail,
                'fee_type' => $request->fee_type,
                'fee_amount' => $request->fee_amount,
                'event_flyer' => $flyerPath,
                'event_gallery' => $galleryPaths,
                'event_doc' => $docPath,
                'status' => $request->status,
            ]);

            foreach ($request->dates as $date) {
                $event->dates()->create([
                    'event_date' => $date['event_date'],
                    'event_time' => $date['event_time'],
                ]);
            }

            return redirect()->back()->with('success', 'Event created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create event');
        }
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'event_name' => 'required|string|max:255',
            'event_detail' => 'required|string',
            'fee_type' => 'required|in:free,paid',
            'fee_amount' => 'required_if:fee_type,paid|nullable|numeric|min:0',
            'event_flyer' => 'nullable|image|max:2048',
            'event_gallery.*' => 'nullable|image|max:2048',
            'event_doc' => 'nullable|mimes:pdf,doc,docx|max:10240',
            'status' => 'required|in:coming_soon,running,closed',
            'dates' => 'required|array|min:1',
            'dates.*.event_date' => 'required|date',
            'dates.*.event_time' => 'required'
        ]);

        try {
            if ($request->hasFile('event_flyer')) {
                if ($event->event_flyer && Storage::disk('public')->exists($event->event_flyer)) {
                    Storage::disk('public')->delete($event->event_flyer);
                }
                $flyerPath = $request->file('event_flyer')->store('events/flyers', 'public');
                $event->event_flyer = $flyerPath;
            }

            if ($request->hasFile('event_gallery')) {
                // Delete old gallery images
                if ($event->event_gallery) {
                    foreach ($event->event_gallery as $oldImage) {
                        if (Storage::disk('public')->exists($oldImage)) {
                            Storage::disk('public')->delete($oldImage);
                        }
                    }
                }

                $galleryPaths = [];
                foreach ($request->file('event_gallery') as $image) {
                    $galleryPaths[] = $image->store('events/gallery', 'public');
                }
                $event->event_gallery = $galleryPaths;
            }

            if ($request->hasFile('event_doc')) {
                if ($event->event_doc && Storage::disk('public')->exists($event->event_doc)) {
                    Storage::disk('public')->delete($event->event_doc);
                }
                $docPath = $request->file('event_doc')->store('events/docs', 'public');
                $event->event_doc = $docPath;
            }

            $event->update([
                'department_id' => $request->department_id,
                'event_name' => $request->event_name,
                'event_detail' => $request->event_detail,
                'fee_type' => $request->fee_type,
                'fee_amount' => $request->fee_amount,
                'status' => $request->status,
            ]);

            // Update dates
            $event->dates()->delete();
            foreach ($request->dates as $date) {
                $event->dates()->create([
                    'event_date' => $date['event_date'],
                    'event_time' => $date['event_time'],
                ]);
            }

            return redirect()->back()->with('success', 'Event updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update event');
        }
    }

    public function destroy(Event $event)
    {
        try {
            // Delete associated files
            if ($event->event_flyer && Storage::disk('public')->exists($event->event_flyer)) {
                Storage::disk('public')->delete($event->event_flyer);
            }

            if ($event->event_gallery) {
                foreach ($event->event_gallery as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }

            if ($event->event_doc && Storage::disk('public')->exists($event->event_doc)) {
                Storage::disk('public')->delete($event->event_doc);
            }

            $event->dates()->delete();
            $event->delete();

            return redirect()->back()->with('success', 'Event deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete event');
        }
    }

    public function show(Event $event)
    {
        return Inertia::render('Events/Show', [
            'event' => $event->load(['department', 'dates']),
        ]);
    }
}