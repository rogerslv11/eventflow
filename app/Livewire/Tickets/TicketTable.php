<?php

namespace App\Livewire\Tickets;

use App\Enums\TicketType as TicketTypeEnum;
use App\Models\Event;
use App\Models\TicketType;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Ingressos')]
#[Layout('layouts.app')]
class TicketTable extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterType = '';

    // Form fields
    public ?int $event_id = null;

    public string $name = '';

    public string $description = '';

    public string $type = 'paid';

    public string $price = '';

    public string $quantity = '';

    public int $min_per_order = 1;

    public int $max_per_order = 10;

    public ?string $sales_start_at = null;

    public ?string $sales_end_at = null;

    public bool $is_active = true;

    public bool $showForm = false;

    public ?int $editingId = null;

    protected $queryString = ['search', 'filterType'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function openForm(?TicketType $ticketType = null): void
    {
        if ($ticketType) {
            $this->editingId = $ticketType->id;
            $this->event_id = $ticketType->event_id;
            $this->name = $ticketType->name;
            $this->description = $ticketType->description ?? '';
            $this->type = $ticketType->type->value;
            $this->price = (string) $ticketType->price;
            $this->quantity = (string) $ticketType->quantity;
            $this->min_per_order = $ticketType->min_per_order;
            $this->max_per_order = $ticketType->max_per_order;
            $this->sales_start_at = $ticketType->sales_start_at?->format('Y-m-d\TH:i');
            $this->sales_end_at = $ticketType->sales_end_at?->format('Y-m-d\TH:i');
            $this->is_active = $ticketType->is_active;
        } else {
            $this->reset(['event_id', 'name', 'description', 'type', 'price', 'quantity', 'min_per_order', 'max_per_order', 'sales_start_at', 'sales_end_at', 'editingId']);
            $this->type = 'paid';
            $this->price = '0';
            $this->quantity = '100';
            $this->min_per_order = 1;
            $this->max_per_order = 10;
            $this->is_active = true;
        }
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->reset(['name', 'description', 'type', 'price', 'quantity', 'min_per_order', 'max_per_order', 'sales_start_at', 'sales_end_at', 'editingId']);
    }

    public function save(): void
    {
        $data = $this->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(array_column(TicketTypeEnum::cases(), 'value'))],
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'min_per_order' => 'required|integer|min:1',
            'max_per_order' => 'required|integer|min:1',
            'sales_start_at' => 'nullable|date',
            'sales_end_at' => 'nullable|date|after:sales_start_at',
            'is_active' => 'boolean',
        ]);

        $data['sales_start_at'] = $data['sales_start_at'] ?: null;
        $data['sales_end_at'] = $data['sales_end_at'] ?: null;

        if ($this->editingId) {
            TicketType::find($this->editingId)->update($data);
            ActivityLogger::log('updated', TicketType::find($this->editingId), "Ingresso atualizado: {$data['name']}");
            $this->dispatch('toast', type: 'success', title: 'Atualizado', message: 'Ingresso atualizado.');
        } else {
            $ticketType = TicketType::create($data);
            ActivityLogger::log('created', $ticketType, "Ingresso criado: {$ticketType->name}");
            $this->dispatch('toast', type: 'success', title: 'Criado', message: 'Ingresso criado com sucesso.');
        }

        $this->closeForm();
    }

    public function toggleActive(TicketType $ticketType): void
    {
        $ticketType->update(['is_active' => ! $ticketType->is_active]);
        $this->dispatch('toast', type: 'success', title: 'Atualizado', message: 'Status atualizado.');
    }

    public function delete(TicketType $ticketType): void
    {
        if ($ticketType->sold > 0) {
            $this->dispatch('toast', type: 'error', title: 'Erro', message: 'Não é possível excluir um ingresso já vendido.');

            return;
        }

        ActivityLogger::log('deleted', $ticketType, "Ingresso excluído: {$ticketType->name}");
        $ticketType->delete();
        $this->dispatch('toast', type: 'success', title: 'Excluído', message: 'Ingresso removido.');
    }

    public function render()
    {
        $organizationId = Auth::user()->organizations()->first()?->id;

        $ticketTypes = TicketType::query()
            ->with(['event', 'batches'])
            ->withCount('tickets')
            ->when($organizationId, fn ($q) => $q->whereHas('event', fn ($q2) => $q2->where('organization_id', $organizationId)))
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filterType, fn ($q) => $q->where('type', $this->filterType))
            ->latest()
            ->paginate(15);

        $events = Event::when($organizationId, fn ($q) => $q->where('organization_id', $organizationId))
            ->whereIn('status', ['published', 'active', 'finished'])
            ->orderBy('name')
            ->get();

        return view('livewire.tickets.ticket-table', [
            'ticketTypes' => $ticketTypes,
            'events' => $events,
        ]);
    }
}
