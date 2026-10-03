<?php

namespace App\Livewire\Participants;

use App\Models\Event;
use App\Models\Participant;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Participantes')]
#[Layout('layouts.app')]
class ParticipantTable extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterEvent = '';

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $filterCheckin = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterEvent(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCheckin(): void
    {
        $this->resetPage();
    }

    public function cancel(Participant $participant): void
    {
        $participant->update(['status' => 'cancelled']);
        ActivityLogger::log('updated', $participant, "Participante cancelado: {$participant->name}");
        $this->dispatch('toast', type: 'success', title: 'Cancelado', message: 'Participante removido.');
    }

    public function exportCsv(): void
    {
        $this->dispatch('toast', type: 'info', title: 'Exportando', message: 'O arquivo será enviado por e-mail.');
    }

    public function render()
    {
        $organizationId = Auth::user()->organizations()->first()?->id;

        $participants = Participant::query()
            ->with(['event', 'ticket.ticketType'])
            ->when($organizationId, fn (Builder $q) => $q->whereHas('event', fn ($q2) => $q2->where('organization_id', $organizationId))
            )
            ->when($this->search, fn (Builder $q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('document', 'like', "%{$this->search}%")
                    ->orWhere('company', 'like', "%{$this->search}%");
            })
            )
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterCheckin === 'yes', fn ($q) => $q->where('has_checked_in', true))
            ->when($this->filterCheckin === 'no', fn ($q) => $q->where('has_checked_in', false))
            ->latest()
            ->paginate(20);

        $events = Event::when($organizationId, fn ($q) => $q->where('organization_id', $organizationId))
            ->orderBy('name')
            ->get();

        return view('livewire.participants.participant-table', [
            'participants' => $participants,
            'events' => $events,
        ]);
    }
}
