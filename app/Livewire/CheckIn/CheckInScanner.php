<?php

namespace App\Livewire\CheckIn;

use App\Models\CheckIn;
use App\Models\Event;
use App\Services\CheckInService;
use App\Services\StatsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Check-in')]
#[Layout('layouts.app')]
class CheckInScanner extends Component
{
    public ?int $eventId = null;

    public string $code = '';

    public ?array $lastResult = null;

    public array $recentCheckIns = [];

    #[Url]
    public string $filter = '';

    public function mount(): void
    {
        $this->loadRecentCheckIns();
    }

    public function loadRecentCheckIns(): void
    {
        $orgId = Auth::user()->organizations()->first()?->id;
        $query = CheckIn::with(['ticket', 'event'])
            ->where('status', 'success')
            ->latest('checked_in_at');

        if ($orgId) {
            $query->whereHas('event', fn ($q) => $q->where('organization_id', $orgId));
        }

        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        }

        $this->recentCheckIns = $query->limit(10)->get()->toArray();
    }

    public function checkIn(): void
    {
        $this->validate(['code' => 'required|string|min:3']);

        $service = app(CheckInService::class);
        $result = $service->checkInByCode(trim($this->code), $this->eventId);

        $this->lastResult = [
            'status' => $result['status'],
            'message' => $result['message'],
            'ticket' => $result['ticket'] ? [
                'code' => $result['ticket']->code,
                'name' => $result['ticket']->participant_name,
                'email' => $result['ticket']->participant_email,
                'event' => $result['ticket']->event->name,
                'type' => $result['ticket']->ticketType?->name,
            ] : null,
            'time' => now()->format('H:i:s'),
        ];

        $this->code = '';
        $this->loadRecentCheckIns();

        // Dispatch toast
        $this->dispatch('toast',
            type: $result['status'] === 'success' ? 'success' : 'warning',
            title: match ($result['status']) {
                'success' => 'Check-in confirmado',
                'duplicate' => 'Check-in duplicado',
                'invalid' => 'Ingresso inválido',
                default => 'Atenção',
            },
            message: $result['message']
        );
    }

    public function updatedEventId(): void
    {
        $this->loadRecentCheckIns();
    }

    public function render()
    {
        $organizationId = Auth::user()->organizations()->first()?->id;

        $events = Event::when($organizationId, fn ($q) => $q->where('organization_id', $organizationId))
            ->whereIn('status', ['published', 'active', 'finished'])
            ->orderBy('starts_at', 'desc')
            ->limit(20)
            ->get(['id', 'name', 'starts_at']);

        $statsService = app(StatsService::class);
        $stats = $statsService->eventCheckInStats($this->eventId);
        $byHour = $statsService->checkInsByHour($this->eventId);

        return view('livewire.check-in.check-in-scanner', [
            'events' => $events,
            'stats' => $stats,
            'byHour' => $byHour,
        ]);
    }
}
