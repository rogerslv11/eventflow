<?php

namespace App\Livewire\Team;

use App\Models\Event;
use App\Models\EventStaff;
use App\Models\StaffRole;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Equipe')]
#[Layout('layouts.app')]
class TeamIndex extends Component
{
    use WithPagination;

    #[Url]
    public ?int $filterEvent = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $document = '';

    public string $type = 'employee';

    public string $status = 'active';

    public ?int $staff_role_id = null;

    public string $notes = '';

    public ?int $editingId = null;

    public bool $showForm = false;

    public function updatingFilterEvent(): void
    {
        $this->resetPage();
    }

    public function openForm(?EventStaff $member = null): void
    {
        if ($member) {
            $this->editingId = $member->id;
            $this->fill($member->toArray());
            $this->filterEvent = $member->event_id;
        } else {
            $this->reset(['name', 'email', 'phone', 'document', 'type', 'status', 'staff_role_id', 'notes', 'editingId']);
            $this->type = 'employee';
            $this->status = 'active';
        }
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->reset(['name', 'email', 'phone', 'document', 'type', 'status', 'staff_role_id', 'notes', 'editingId']);
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'document' => 'nullable|string|max:20',
            'type' => 'required|in:employee,freelancer,volunteer',
            'status' => 'required|in:active,inactive',
            'staff_role_id' => 'nullable|exists:staff_roles,id',
            'notes' => 'nullable|string',
            'filterEvent' => 'required|exists:events,id',
        ]);

        $data['event_id'] = $data['filterEvent'];
        unset($data['filterEvent']);

        if ($this->editingId) {
            EventStaff::find($this->editingId)->update($data);
            ActivityLogger::log('updated', EventStaff::find($this->editingId), "Membro atualizado: {$data['name']}");
            $this->dispatch('toast', type: 'success', title: 'Atualizado', message: 'Membro atualizado.');
        } else {
            $member = EventStaff::create($data);
            ActivityLogger::log('created', $member, "Membro adicionado: {$member->name}");
            $this->dispatch('toast', type: 'success', title: 'Adicionado', message: 'Membro adicionado à equipe.');
        }

        $this->closeForm();
    }

    public function delete(EventStaff $member): void
    {
        ActivityLogger::log('deleted', $member, "Membro removido: {$member->name}");
        $member->delete();
        $this->dispatch('toast', type: 'success', title: 'Removido', message: 'Membro removido da equipe.');
    }

    public function render()
    {
        $orgId = Auth::user()->organizations()->first()?->id;

        $events = Event::where('organization_id', $orgId)->orderBy('name')->get(['id', 'name']);
        $roles = $this->filterEvent
            ? StaffRole::where('event_id', $this->filterEvent)->get()
            : collect();

        $staff = EventStaff::query()
            ->with(['event', 'role'])
            ->whereHas('event', fn ($q) => $q->where('organization_id', $orgId))
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent))
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.team.team-index', [
            'events' => $events,
            'staff' => $staff,
            'roles' => $roles,
        ]);
    }
}
