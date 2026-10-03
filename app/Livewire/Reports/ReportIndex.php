<?php

namespace App\Livewire\Reports;

use App\Enums\OrderStatus;
use App\Models\CheckIn;
use App\Models\Coupon;
use App\Models\Event;
use App\Models\Order;
use App\Models\Participant;
use App\Models\Ticket;
use App\Services\StatsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Relatórios')]
#[Layout('layouts.app')]
class ReportIndex extends Component
{
    #[Url]
    public ?int $filterEvent = null;

    public string $period = '30'; // 7, 30, 90, all

    public string $reportType = 'overview'; // overview, sales, attendees, checkins, coupons

    public function updatedFilterEvent(): void
    {
        // refresh
    }

    public function exportCsv(): void
    {
        $this->dispatch('toast', type: 'info', title: 'Exportando', message: 'Arquivo será gerado em instantes.');
    }

    public function render()
    {
        $orgId = Auth::user()->organizations()->first()?->id;
        $organizationId = $orgId;

        $events = Event::where('organization_id', $orgId)
            ->whereIn('status', ['published', 'active', 'finished'])
            ->orderBy('starts_at', 'desc')
            ->get(['id', 'name']);

        $startDate = match ($this->period) {
            '7' => now()->subDays(7),
            '30' => now()->subDays(30),
            '90' => now()->subDays(90),
            'all' => now()->subYears(5),
            default => now()->subDays(30),
        };

        $orderQuery = Order::where('organization_id', $orgId)
            ->where('created_at', '>=', $startDate);
        $ticketQuery = Ticket::whereHas('event', fn ($q) => $q->where('organization_id', $orgId))
            ->where('created_at', '>=', $startDate);
        $checkInQuery = CheckIn::whereHas('event', fn ($q) => $q->where('organization_id', $orgId))
            ->where('checked_in_at', '>=', $startDate);
        $participantQuery = Participant::whereHas('event', fn ($q) => $q->where('organization_id', $orgId))
            ->where('created_at', '>=', $startDate);

        if ($this->filterEvent) {
            $orderQuery->where('event_id', $this->filterEvent);
            $ticketQuery->where('event_id', $this->filterEvent);
            $checkInQuery->where('event_id', $this->filterEvent);
            $participantQuery->where('event_id', $this->filterEvent);
        }

        $stats = [
            'orders_total' => (clone $orderQuery)->count(),
            'orders_paid' => (clone $orderQuery)->where('status', OrderStatus::Paid)->count(),
            'revenue' => (clone $orderQuery)->where('status', OrderStatus::Paid)->sum('total'),
            'tickets_sold' => (clone $ticketQuery)->count(),
            'participants' => (clone $participantQuery)->count(),
            'check_ins' => (clone $checkInQuery)->where('status', 'success')->count(),
            'attendance_rate' => 0,
        ];
        $stats['attendance_rate'] = $stats['tickets_sold'] > 0 ? round(($stats['check_ins'] / $stats['tickets_sold']) * 100, 1) : 0;

        // Sales by event
        $salesByEvent = Event::where('organization_id', $orgId)
            ->withSum(['orders' => fn ($q) => $q->where('status', OrderStatus::Paid)], 'total')
            ->withCount('tickets')
            ->when($this->filterEvent, fn ($q) => $q->where('id', $this->filterEvent))
            ->orderByDesc('orders_sum_total')
            ->limit(10)
            ->get();

        // Top coupons
        $topCoupons = Coupon::where('organization_id', $orgId)
            ->withCount('usages')
            ->orderByDesc('usage_count')
            ->limit(10)
            ->get();

        // Check-ins by day
        $checkInsByDay = (clone $checkInQuery)
            ->selectRaw('DATE(checked_in_at) as date, COUNT(*) as count')
            ->where('status', 'success')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $statsService = app(StatsService::class);
        $revenueChart = $statsService->revenueChart((int) $this->period === 0 ? 90 : (int) $this->period, $organizationId);
        $ordersChart = [$stats['orders_paid'], max(0, $stats['orders_total'] - $stats['orders_paid'])];

        return view('livewire.reports.report-index', [
            'events' => $events,
            'stats' => $stats,
            'salesByEvent' => $salesByEvent,
            'topCoupons' => $topCoupons,
            'checkInsByDay' => $checkInsByDay,
            'revenueChart' => $revenueChart,
            'ordersChart' => $ordersChart,
        ]);
    }
}
