<?php

namespace App\Livewire\Schedule;

use App\Enums\ScheduleType;
use App\Models\Event;
use App\Models\Schedule;
use App\Models\Speaker;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Programação')]
#[Layout('layouts.app')]
class ScheduleIndex extends Component
{
    use WithPagination;

    #[Url]
    public ?int $filterEvent = null;

    public string $title = '';

    public string $description = '';

    public string $type = 'talk';

    public string $room = '';

    public string $starts_at = '';

    public string $ends_at = '';

    public ?int $speaker_id = null;

    public ?int $capacity = null;

    public bool $is_featured = false;

    public ?int $editingId = null;

    public bool $showForm = false;

    public function updatingFilterEvent(): void
    {
        $this->resetPage();
    }

    public function openForm(?Schedule $schedule = null): void
    {
        if ($schedule) {
            $this->editingId = $schedule->id;
            $this->fill($schedule->toArray());
            $this->type = $schedule->type->value;
            $this->starts_at = $schedule->starts_at?->format('Y-m-d\TH:i') ?? '';
            $this->ends_at = $schedule->ends_at?->format('Y-m-d\TH:i') ?? '';
            $this->filterEvent = $schedule->event_id;
        } else {
            $this->reset(['title', 'description', 'type', 'room', 'starts_at', 'ends_at', 'speaker_id', 'capacity', 'is_featured', 'editingId']);
            $this->type = 'talk';
        }
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->reset(['title', 'description', 'type', 'room', 'starts_at', 'ends_at', 'speaker_id', 'capacity', 'is_featured', 'editingId']);
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(array_column(ScheduleType::cases(), 'value'))],
            'room' => 'nullable|string|max:255',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'speaker_id' => 'nullable|exists:speakers,id',
            'capacity' => 'nullable|integer|min:1',
            'is_featured' => 'boolean',
            'filterEvent' => 'required|exists:events,id',
        ]);

        $data['event_id'] = $data['filterEvent'];
        unset($data['filterEvent']);

        if ($this->editingId) {
            Schedule::find($this->editingId)->update($data);
            ActivityLogger::log('updated', Schedule::find($this->editingId), "Programação atualizada: {$data['title']}");
            $this->dispatch('toast', type: 'success', title: 'Atualizado', message: 'Atividade atualizada.');
        } else {
            $schedule = Schedule::create($data);
            ActivityLogger::log('created', $schedule, "Atividade criada: {$schedule->title}");
            $this->dispatch('toast', type: 'success', title: 'Criado', message: 'Atividade adicionada.');
        }

        $this->closeForm();
    }

    public function delete(Schedule $schedule): void
    {
        ActivityLogger::log('deleted', $schedule, "Atividade excluída: {$schedule->title}");
        $schedule->delete();
        $this->dispatch('toast', type: 'success', title: 'Excluído', message: 'Atividade removida.');
    }

    public function render()
    {
        $orgId = Auth::user()->organizations()->first()?->id;

        $events = Event::where('organization_id', $orgId)
            ->whereIn('status', ['published', 'active', 'finished'])
            ->orderBy('starts_at', 'desc')
            ->get(['id', 'name', 'starts_at']);

        $schedules = Schedule::query()
            ->with(['event', 'speaker'])
            ->whereHas('event', fn ($q) => $q->where('organization_id', $orgId))
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent))
            ->orderBy('starts_at', 'asc')
            ->paginate(20);

        $speakers = Speaker::where('organization_id', $orgId)->get(['id', 'name']);

        return view('livewire.schedule.schedule-index', [
            'events' => $events,
            'schedules' => $schedules,
            'speakers' => $speakers,
        ]);
    }
}
