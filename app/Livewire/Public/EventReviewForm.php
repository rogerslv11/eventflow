<?php

namespace App\Livewire\Public;

use App\Models\Event;
use App\Models\EventReview;
use Livewire\Component;

class EventReviewForm extends Component
{
    public Event $event;

    public string $name = '';

    public string $email = '';

    public int $rating = 5;

    public string $title = '';

    public string $comment = '';

    public bool $sent = false;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function submit(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:2000',
        ]);

        EventReview::create([
            ...$data,
            'organization_id' => $this->event->organization_id,
            'event_id' => $this->event->id,
            'is_approved' => true,
        ]);

        $this->sent = true;
        $this->reset(['name', 'email', 'title', 'comment']);
        $this->rating = 5;
    }

    public function render()
    {
        $reviews = EventReview::where('event_id', $this->event->id)
            ->where('is_approved', true)
            ->latest()
            ->take(6)
            ->get();

        $average = EventReview::averageForEvent($this->event->id);

        return view('livewire.public.event-review-form', [
            'reviews' => $reviews,
            'average' => round($average, 1),
            'total' => EventReview::where('event_id', $this->event->id)->where('is_approved', true)->count(),
        ]);
    }
}
