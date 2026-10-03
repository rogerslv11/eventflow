<?php

namespace App\Livewire\Waitlist;

use App\Models\Event;
use App\Models\Waitlist;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Lista de Espera')]
#[Layout('layouts.app')]
class WaitlistTable extends Component
{
    use WithPagination;

    #[Url]
    public string $filterEvent = '';

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function notify(Waitlist $entry): void
    {
        $entry->update(['status' => Waitlist::STATUS_NOTIFIED, 'notified_at' => now()]);
        ActivityLogger::log('updated', $entry, "Lista de espera: {$entry->email} notificado");
        $this->dispatch('toast', type: 'success', title: 'Notificado', message: 'Marque o contato com o participante.');
    }

    public function convert(Waitlist $entry): void
    {
        $entry->update(['status' => Waitlist::STATUS_CONVERTED]);
        $this->dispatch('toast', type: 'success', title: 'Convertido', message: 'Vaga convertida em venda.');
    }

    public function cancelEntry(Waitlist $entry): void
    {
        $entry->update(['status' => Waitlist::STATUS_CANCELLED]);
        $this->dispatch('toast', type: 'warning', title: 'Cancelado', message: 'Registro cancelado.');
    }

    public function delete(Waitlist $entry): void
    {
        $entry->delete();
        $this->dispatch('toast', type: 'success', title: 'Excluído', message: 'Registro removido.');
    }

    public function render()
    {
        $orgId = Auth::user()->organizations()->first()?->id;

        $entries = Waitlist::query()
            ->with(['event', 'ticketType'])
            ->where('organization_id', $orgId)
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(15);

        $waiting = Waitlist::where('organization_id', $orgId)->where('status', Waitlist::STATUS_WAITING)->count();
        $events = Event::where('organization_id', $orgId)->orderBy('name')->get(['id', 'name']);

        return view('livewire.waitlist.waitlist-table', [
            'entries' => $entries,
            'events' => $events,
            'waiting' => $waiting,
            'statuses' => Waitlist::statuses(),
        ]);
    }
}
