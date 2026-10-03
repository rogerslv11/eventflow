<?php

namespace App\Livewire\Events;

use App\Enums\EventStatus;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Eventos')]
#[Layout('layouts.app')]
class EventIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $category = '';

    public string $sortField = 'starts_at';

    public string $sortDirection = 'desc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function publish(Event $event): void
    {
        $this->authorize('publish', $event);
        $event->update([
            'status' => EventStatus::Published,
            'published_at' => now(),
        ]);
        $this->dispatch('toast', type: 'success', title: 'Evento publicado', message: "{$event->name} foi publicado.");
    }

    public function unpublish(Event $event): void
    {
        $this->authorize('update', $event);
        $event->update([
            'status' => EventStatus::Draft,
            'published_at' => null,
        ]);
        $this->dispatch('toast', type: 'success', title: 'Evento despublicado', message: "{$event->name} voltou para rascunho.");
    }

    public function cancel(Event $event): void
    {
        $this->authorize('update', $event);
        $event->update(['status' => EventStatus::Cancelled]);
        $this->dispatch('toast', type: 'warning', title: 'Evento cancelado', message: "{$event->name} foi cancelado.");
    }

    public function archive(Event $event): void
    {
        $this->authorize('delete', $event);
        $event->update(['status' => EventStatus::Archived]);
        $this->dispatch('toast', type: 'success', title: 'Evento arquivado', message: "{$event->name} foi arquivado.");
    }

    public function duplicate(Event $event): void
    {
        $this->authorize('duplicate', $event);

        $new = $event->replicate();
        $new->name = $event->name.' (Cópia)';
        $new->slug = Str::slug($new->name).'-'.Str::random(4);
        $new->status = EventStatus::Draft;
        $new->published_at = null;
        $new->tickets_sold = 0;
        $new->check_ins_count = 0;
        $new->save();

        $this->dispatch('toast', type: 'success', title: 'Evento duplicado', message: "{$new->name} foi criado.");
    }

    public function delete(Event $event): void
    {
        $this->authorize('delete', $event);
        $event->delete();
        $this->dispatch('toast', type: 'success', title: 'Evento excluído', message: "{$event->name} foi excluído.");
    }

    public function render()
    {
        $organizationId = Auth::user()->organizations()->first()?->id;

        $events = Event::query()
            ->with(['category', 'organization'])
            ->withCount(['tickets', 'participants', 'checkIns'])
            ->when($organizationId, fn (Builder $q) => $q->where('organization_id', $organizationId))
            ->when($this->search, fn (Builder $q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('city', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            })
            )
            ->when($this->status, fn (Builder $q) => $q->where('status', $this->status))
            ->when($this->category, fn (Builder $q) => $q->where('category_id', $this->category))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(12);

        $categories = Category::where('is_active', true)->get();

        return view('livewire.events.event-index', [
            'events' => $events,
            'categories' => $categories,
        ]);
    }
}
