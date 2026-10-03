<?php

namespace App\Livewire\Public;

use App\Models\Event;
use App\Models\Waitlist;
use Livewire\Component;

class WaitlistForm extends Component
{
    public Event $event;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public int $quantity = 1;

    public bool $joined = false;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function submit(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        Waitlist::updateOrCreate(
            ['event_id' => $this->event->id, 'email' => $data['email']],
            [
                ...$data,
                'organization_id' => $this->event->organization_id,
                'status' => Waitlist::STATUS_WAITING,
            ]
        );

        $this->joined = true;
    }

    public function render()
    {
        return view('livewire.public.waitlist-form', [
            'totalWaiting' => Waitlist::where('event_id', $this->event->id)
                ->where('status', Waitlist::STATUS_WAITING)->count(),
        ]);
    }
}
