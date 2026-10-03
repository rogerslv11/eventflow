<?php

namespace App\Livewire\Tasks;

use App\Models\Event;
use App\Models\EventTask;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Tarefas')]
#[Layout('layouts.app')]
class TaskBoard extends Component
{
    #[Url]
    public string $filterEvent = '';

    public string $title = '';

    public string $description = '';

    public string $priority = 'medium';

    public string $due_date = '';

    public string $assigned_to = '';

    public string $event_id = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public function openForm(?EventTask $task = null): void
    {
        if ($task && $task->id) {
            $this->editingId = $task->id;
            $this->title = $task->title;
            $this->description = $task->description ?? '';
            $this->priority = $task->priority;
            $this->due_date = $task->due_date?->format('Y-m-d') ?? '';
            $this->assigned_to = $task->assigned_to ?? '';
            $this->event_id = (string) $task->event_id;
        } else {
            $this->reset(['title', 'description', 'assigned_to', 'editingId']);
            $this->priority = 'medium';
            $this->due_date = '';
            $this->event_id = $this->filterEvent;
        }
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->reset(['title', 'description', 'assigned_to', 'editingId']);
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|string|max:255',
            'event_id' => 'required|exists:events,id',
        ]);

        $data['organization_id'] = Auth::user()->organizations()->first()?->id;
        $data['status'] = EventTask::STATUS_TODO;
        $data['due_date'] = $data['due_date'] ?: null;

        if ($this->editingId) {
            $task = EventTask::find($this->editingId);
            $task->update($data);
            $this->dispatch('toast', type: 'success', title: 'Atualizada', message: 'Tarefa atualizada.');
        } else {
            $data['sort_order'] = EventTask::where('event_id', $data['event_id'])->max('sort_order') + 1;
            $task = EventTask::create($data);
            ActivityLogger::log('created', $task, "Tarefa criada: {$task->title}");
            $this->dispatch('toast', type: 'success', title: 'Criada', message: 'Tarefa criada.');
        }

        $this->closeForm();
    }

    public function advance(EventTask $task): void
    {
        $next = match ($task->status) {
            EventTask::STATUS_TODO => EventTask::STATUS_DOING,
            EventTask::STATUS_DOING => EventTask::STATUS_DONE,
            default => EventTask::STATUS_TODO,
        };

        $task->update([
            'status' => $next,
            'completed_at' => $next === EventTask::STATUS_DONE ? now() : null,
        ]);
    }

    public function delete(EventTask $task): void
    {
        $task->delete();
        $this->dispatch('toast', type: 'success', title: 'Excluída', message: 'Tarefa removida.');
    }

    public function render()
    {
        $orgId = Auth::user()->organizations()->first()?->id;

        $tasks = EventTask::query()
            ->with('event')
            ->where('organization_id', $orgId)
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent))
            ->orderBy('due_date')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('status');

        $events = Event::where('organization_id', $orgId)->orderBy('name')->get(['id', 'name']);

        $total = EventTask::where('organization_id', $orgId)
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent))->count();
        $done = EventTask::where('organization_id', $orgId)->where('status', EventTask::STATUS_DONE)
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent))->count();
        $overdue = EventTask::where('organization_id', $orgId)->where('status', '!=', EventTask::STATUS_DONE)
            ->whereDate('due_date', '<', now()->toDateString())
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent))->count();

        return view('livewire.tasks.task-board', [
            'grouped' => $tasks,
            'events' => $events,
            'total' => $total,
            'done' => $done,
            'overdue' => $overdue,
            'progress' => $total > 0 ? round(($done / $total) * 100) : 0,
        ]);
    }
}
