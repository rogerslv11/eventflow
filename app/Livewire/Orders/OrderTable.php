<?php

namespace App\Livewire\Orders;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pedidos')]
#[Layout('layouts.app')]
class OrderTable extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $filterEvent = '';

    public ?int $detailId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterEvent(): void
    {
        $this->resetPage();
    }

    public function showDetail(int $id): void
    {
        $this->detailId = $id;
    }

    public function closeDetail(): void
    {
        $this->detailId = null;
    }

    public function markPaid(Order $order): void
    {
        $order->update(['status' => OrderStatus::Paid, 'paid_at' => now()]);
        ActivityLogger::log('updated', $order, "Pedido {$order->code} marcado como pago");
        $this->dispatch('toast', type: 'success', title: 'Atualizado', message: 'Pedido marcado como pago.');
    }

    public function cancel(Order $order): void
    {
        $order->update(['status' => OrderStatus::Cancelled, 'cancelled_at' => now()]);
        ActivityLogger::log('updated', $order, "Pedido {$order->code} cancelado");
        $this->dispatch('toast', type: 'warning', title: 'Cancelado', message: 'Pedido cancelado.');
    }

    public function refund(Order $order): void
    {
        $order->update(['status' => OrderStatus::Refunded, 'refunded_at' => now()]);
        ActivityLogger::log('updated', $order, "Pedido {$order->code} reembolsado");
        $this->dispatch('toast', type: 'success', title: 'Reembolsado', message: 'Pedido reembolsado.');
    }

    public function render()
    {
        $orgId = Auth::user()->organizations()->first()?->id;

        $base = Order::query()->where('organization_id', $orgId);

        $stats = [
            'total' => (clone $base)->count(),
            'revenue' => (float) (clone $base)->where('status', OrderStatus::Paid)->sum('total'),
            'pending' => (clone $base)->where('status', OrderStatus::Pending)->count(),
            'refunded' => (clone $base)->where('status', OrderStatus::Refunded)->count(),
        ];

        $orders = $base->with(['event', 'items'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('code', 'like', "%{$this->search}%")
                    ->orWhere('customer_name', 'like', "%{$this->search}%")
                    ->orWhere('customer_email', 'like', "%{$this->search}%");
            }))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent))
            ->latest()
            ->paginate(15);

        $events = Event::where('organization_id', $orgId)->orderBy('name')->get(['id', 'name']);

        $detail = $this->detailId ? Order::with(['event', 'items', 'tickets', 'coupon'])->find($this->detailId) : null;

        return view('livewire.orders.order-table', [
            'orders' => $orders,
            'events' => $events,
            'stats' => $stats,
            'detail' => $detail,
            'statuses' => OrderStatus::cases(),
        ]);
    }
}
