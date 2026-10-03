<?php

namespace App\Livewire\Sponsors;

use App\Enums\SponsorTier;
use App\Models\Event;
use App\Models\Sponsor;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Patrocinadores')]
#[Layout('layouts.app')]
class SponsorTable extends Component
{
    use WithFileUploads, WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterTier = '';

    #[Url]
    public string $filterEvent = '';

    public string $name = '';

    public ?int $event_id = null;

    public string $tier = 'bronze';

    public string $website = '';

    public string $description = '';

    public string $contribution = '';

    public bool $is_active = true;

    public $logo;

    public ?int $editingId = null;

    public bool $showForm = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTier(): void
    {
        $this->resetPage();
    }

    public function updatingFilterEvent(): void
    {
        $this->resetPage();
    }

    public function openForm(?Sponsor $sponsor = null): void
    {
        if ($sponsor) {
            $this->editingId = $sponsor->id;
            $this->fill($sponsor->toArray());
            $this->tier = $sponsor->tier->value;
            $this->event_id = $sponsor->event_id;
        } else {
            $this->reset(['name', 'event_id', 'tier', 'website', 'description', 'contribution', 'is_active', 'editingId', 'logo']);
            $this->tier = 'bronze';
            $this->is_active = true;
        }
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->reset(['name', 'event_id', 'tier', 'website', 'description', 'contribution', 'is_active', 'editingId', 'logo']);
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'event_id' => 'nullable|exists:events,id',
            'tier' => ['required', Rule::in(array_column(SponsorTier::cases(), 'value'))],
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'contribution' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data['organization_id'] = Auth::user()->organizations()->first()?->id;
        $data['contribution'] = $data['contribution'] ?: null;
        $data['sort_order'] = SponsorTier::from($data['tier'])->sortOrder();

        if (! empty($data['logo'])) {
            $data['logo_path'] = $data['logo']->store('sponsors', 'public');
        }

        if ($this->editingId) {
            Sponsor::find($this->editingId)->update($data);
            ActivityLogger::log('updated', Sponsor::find($this->editingId), "Patrocinador atualizado: {$data['name']}");
            $this->dispatch('toast', type: 'success', title: 'Atualizado', message: 'Patrocinador atualizado.');
        } else {
            $sponsor = Sponsor::create($data);
            ActivityLogger::log('created', $sponsor, "Patrocinador criado: {$data['name']}");
            $this->dispatch('toast', type: 'success', title: 'Criado', message: 'Patrocinador adicionado.');
        }

        $this->closeForm();
    }

    public function delete(Sponsor $sponsor): void
    {
        ActivityLogger::log('deleted', $sponsor, "Patrocinador excluído: {$sponsor->name}");
        $sponsor->delete();
        $this->dispatch('toast', type: 'success', title: 'Excluído', message: 'Patrocinador removido.');
    }

    public function render()
    {
        $orgId = Auth::user()->organizations()->first()?->id;
        $sponsors = Sponsor::query()
            ->with('event')
            ->where('organization_id', $orgId)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filterTier, fn ($q) => $q->where('tier', $this->filterTier))
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent))
            ->orderBy('sort_order')
            ->orderByDesc('contribution')
            ->paginate(20);

        $events = Event::where('organization_id', $orgId)->orderBy('name')->get(['id', 'name']);

        return view('livewire.sponsors.sponsor-table', [
            'sponsors' => $sponsors,
            'events' => $events,
            'tiers' => SponsorTier::cases(),
        ]);
    }
}
