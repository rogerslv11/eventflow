<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\View\View;

class EventPageController extends Controller
{
    public function show(string $slug): View
    {
        $event = Event::with([
            'organization',
            'category',
            'ticketTypes',
            'speakers',
            'schedules',
            'sponsors',
        ])
            ->where(function ($q) {
                $q->where('is_public', true);
            })
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.event', [
            'event' => $event,
            'title' => $event->name,
        ]);
    }

    public function index(): View
    {
        $events = Event::query()
            ->with('organization')
            ->where('is_public', true)
            ->whereIn('status', ['published', 'active'])
            ->orderBy('starts_at', 'asc')
            ->paginate(12);

        return view('public.events', [
            'events' => $events,
            'title' => 'Eventos',
        ]);
    }
}
