<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Evento')]
#[Layout('layouts.app')]
class EventShow extends Component
{
    public Event $event;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function render()
    {
        $event = $this->event->load([
            'category', 'organization', 'ticketTypes', 'speakers',
            'schedules.speaker', 'sponsors', 'coupons', 'staff.role',
        ]);
        $event->loadCount(['tickets', 'participants', 'checkIns', 'orders']);

        return view('livewire.events.event-show', [
            'event' => $event,
        ]);
    }
}
