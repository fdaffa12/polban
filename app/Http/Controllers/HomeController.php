<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use App\Models\Event;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $aboutUs = AboutUs::first();
        $today = now();

        // Ambil event yang sedang berjalan dan akan datang
        $activeAndUpcomingEvents = Event::with('dates')
            ->whereHas('dates', function ($query) use ($today) {
                $query->where('event_date', '>=', $today->toDateString());
            })
            ->orderBy(function ($query) use ($today) {
                // Custom ordering untuk memprioritaskan event yang sedang berjalan
                $query->select(DB::raw('
                    CASE 
                        WHEN EXISTS (
                            SELECT 1 FROM event_dates 
                            WHERE event_dates.event_id = events.id 
                            AND event_dates.event_date = "' . $today->toDateString() . '"
                        ) THEN 1
                        ELSE 2
                    END
                '));
            })
            ->take(3) // Ambil 3 event aktif/upcoming
            ->get();

        // Jika slot masih tersisa, ambil event yang sudah selesai
        $remainingSlots = 5 - $activeAndUpcomingEvents->count();
        $pastEvents = collect();

        if ($remainingSlots > 0) {
            $pastEvents = Event::with('dates')
                ->whereHas('dates', function ($query) use ($today) {
                    $query->where('event_date', '<', $today->toDateString());
                })
                ->latest()
                ->take($remainingSlots)
                ->get();
        }

        // Gabungkan dan map events
        $events = $activeAndUpcomingEvents->concat($pastEvents);

        $mappedEvents = $events->map(function ($event) use ($today) {
            $eventDates = $event->dates->sortBy('event_date');
            $firstDate = $eventDates->first();
            $lastDate = $eventDates->last();
            
            // Determine status based on event dates
            $status = 'upcoming';
            if ($firstDate->event_date <= $today->toDateString() && $lastDate->event_date >= $today->toDateString()) {
                $status = 'ongoing';
            } elseif ($lastDate->event_date < $today->toDateString()) {
                $status = 'past';
            }

            return [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_detail' => Str::limit(strip_tags($event->event_detail), 150),
                'fee_type' => $event->fee_type,
                'event_flyer' => $event->event_flyer ? "/storage/{$event->event_flyer}" : null,
                'dates' => $event->dates,
                'status' => $status,
                'start_date' => $firstDate ? $firstDate->event_date : null,
                'end_date' => $lastDate && $lastDate->event_date != $firstDate->event_date ? $lastDate->event_date : null
            ];
        });

        return Inertia::render('Home', [
            'canLogin' => true,
            'canRegister' => true,
            'aboutUs' => [
                'title' => $aboutUs->au_title ?? '',
                'description' => $aboutUs->au_desc ? strip_tags($aboutUs->au_desc) : '',
                'image' => $aboutUs->au_image ? "/storage/{$aboutUs->au_image}" : null,
            ],
            'events' => $mappedEvents
        ]);
    }
}