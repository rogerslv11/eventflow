<?php

namespace App\Livewire\Expenses;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\EventExpense;
use App\Models\Order;
use App\Models\Vendor;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Despesas')]
#[Layout('layouts.app')]
class ExpenseTable extends Component
{
    use WithPagination;

    #[Url]
    public string $filterEvent = '';

    #[Url]
    public string $filterStatus = '';

    public string $title = '';

    public string $description = '';

    public string $category = 'geral';

    public string $amount = '';

    public string $status = 'pending';

    public string $due_date = '';

    public string $event_id = '';

    public string $vendor_id = '';

    public ?int $editingId = null;

    public bool $showForm = false;

    public function updatingFilterEvent(): void
    {
        $this->resetPage();
    }

    public function openForm(?EventExpense $expense = null): void
    {
        if ($expense && $expense->id) {
            $this->editingId = $expense->id;
            $this->title = $expense->title;
            $this->description = $expense->description ?? '';
            $this->category = $expense->category;
            $this->amount = (string) $expense->amount;
            $this->status = $expense->status;
            $this->due_date = $expense->due_date?->format('Y-m-d') ?? '';
            $this->event_id = (string) $expense->event_id;
            $this->vendor_id = $expense->vendor_id ? (string) $expense->vendor_id : '';
        } else {
            $this->reset(['title', 'description', 'amount', 'editingId', 'vendor_id']);
            $this->category = 'geral';
            $this->status = 'pending';
            $this->event_id = $this->filterEvent;
            $this->due_date = '';
        }
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->reset(['title', 'description', 'amount', 'editingId', 'vendor_id']);
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,cancelled',
            'due_date' => 'nullable|date',
            'event_id' => 'required|exists:events,id',
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);

        $data['vendor_id'] = $data['vendor_id'] ?: null;
        $data['due_date'] = $data['due_date'] ?: null;
        $data['organization_id'] = Auth::user()->organizations()->first()?->id;
        $data['paid_at'] = $data['status'] === EventExpense::STATUS_PAID ? now() : null;

        if ($this->editingId) {
            EventExpense::find($this->editingId)->update($data);
            $this->dispatch('toast', type: 'success', title: 'Atualizada', message: 'Despesa atualizada.');
        } else {
            $expense = EventExpense::create($data);
            ActivityLogger::log('created', $expense, "Despesa criada: {$expense->title} - R$ {$expense->amount}");
            $this->dispatch('toast', type: 'success', title: 'Criada', message: 'Despesa registrada.');
        }

        $this->closeForm();
    }

    public function markPaid(EventExpense $expense): void
    {
        $expense->update(['status' => EventExpense::STATUS_PAID, 'paid_at' => now()]);
        $this->dispatch('toast', type: 'success', title: 'Paga', message: 'Despesa marcada como paga.');
    }

    public function delete(EventExpense $expense): void
    {
        $expense->delete();
        $this->dispatch('toast', type: 'success', title: 'Excluída', message: 'Despesa removida.');
    }

    public function render()
    {
        $orgId = Auth::user()->organizations()->first()?->id;

        $base = EventExpense::query()->where('organization_id', $orgId)
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent));

        $stats = [
            'paid' => (float) (clone $base)->where('status', EventExpense::STATUS_PAID)->sum('amount'),
            'pending' => (float) (clone $base)->where('status', EventExpense::STATUS_PENDING)->sum('amount'),
            'count' => (clone $base)->count(),
        ];

        $revenue = Order::where('organization_id', $orgId)
            ->where('status', OrderStatus::Paid)
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent))
            ->sum('total');
        $stats['revenue'] = (float) $revenue;
        $stats['profit'] = $stats['revenue'] - $stats['paid'];

        $expenses = EventExpense::query()
            ->with(['event', 'vendor'])
            ->where('organization_id', $orgId)
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(15);

        $events = Event::where('organization_id', $orgId)->orderBy('name')->get(['id', 'name']);
        $vendors = Vendor::where('organization_id', $orgId)->orderBy('name')->get(['id', 'name']);

        return view('livewire.expenses.expense-table', [
            'expenses' => $expenses,
            'events' => $events,
            'vendors' => $vendors,
            'stats' => $stats,
            'categories' => EventExpense::categories(),
            'statuses' => EventExpense::statuses(),
        ]);
    }
}
