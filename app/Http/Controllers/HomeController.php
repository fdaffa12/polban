<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use App\Models\Event;
use Inertia\Inertia;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $aboutUs = AboutUs::first();

        $events = Event::with('dates')
            ->latest()
            ->take(5)
            ->get();

        $mappedEvents = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'event_detail' => Str::limit(strip_tags($event->event_detail), 150),
                'fee_type' => $event->fee_type,
                'event_flyer' => $event->event_flyer ? "/storage/{$event->event_flyer}" : null,
                'dates' => $event->dates
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