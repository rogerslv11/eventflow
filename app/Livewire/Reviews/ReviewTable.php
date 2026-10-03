<?php

namespace App\Livewire\Reviews;

use App\Models\Event;
use App\Models\EventReview;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Avaliações')]
#[Layout('layouts.app')]
class ReviewTable extends Component
{
    use WithPagination;

    #[Url]
    public string $filterEvent = '';

    #[Url]
    public string $filterRating = '';

    public function updatingFilterEvent(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRating(): void
    {
        $this->resetPage();
    }

    public function toggleApproval(EventReview $review): void
    {
        $review->update(['is_approved' => ! $review->is_approved]);
        $this->dispatch('toast', type: 'success', title: 'Atualizado', message: 'Visibilidade alterada.');
    }

    public function delete(EventReview $review): void
    {
        ActivityLogger::log('deleted', $review, "Avaliação de {$review->name} excluída");
        $review->delete();
        $this->dispatch('toast', type: 'success', title: 'Excluída', message: 'Avaliação removida.');
    }

    public function render()
    {
        $orgId = Auth::user()->organizations()->first()?->id;

        $base = EventReview::query()->where('organization_id', $orgId)->where('is_approved', true);

        $stats = [
            'total' => (clone $base)->count(),
            'average' => round((float) (clone $base)->avg('rating'), 1),
            'five_star' => (clone $base)->where('rating', 5)->count(),
            'nps' => $this->filterEvent
                ? EventReview::npsForEvent((int) $this->filterEvent)
                : 0,
        ];

        $reviews = EventReview::query()
            ->with('event')
            ->where('organization_id', $orgId)
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent))
            ->when($this->filterRating, fn ($q) => $q->where('rating', $this->filterRating))
            ->latest()
            ->paginate(15);

        $events = Event::where('organization_id', $orgId)->orderBy('name')->get(['id', 'name']);

        return view('livewire.reviews.review-table', [
            'reviews' => $reviews,
            'events' => $events,
            'stats' => $stats,
        ]);
    }
}
