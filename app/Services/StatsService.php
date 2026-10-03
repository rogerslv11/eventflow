<?php

namespace App\Services;

use App\Enums\EventStatus;
use App\Models\ActivityLog;
use App\Models\CheckIn;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsService
{
    public function dashboardStats(?int $organizationId = null): array
    {
        $eventQuery = Event::query();
        $orderQuery = Order::query();
        $ticketQuery = Ticket::query();
        $checkInQuery = CheckIn::query();

        if ($organizationId) {
            $eventQuery->where('organization_id', $organizationId);
            $orderQuery->where('organization_id', $organizationId);
            $ticketQuery->whereHas('event', fn ($q) => $q->where('organization_id', $organizationId));
            $checkInQuery->whereHas('event', fn ($q) => $q->where('organization_id', $organizationId));
        }

        $totalEvents = (clone $eventQuery)->count();
        $activeEvents = (clone $eventQuery)->whereIn('status', [EventStatus::Published->value, EventStatus::Active->value])->count();
        $upcomingEvents = (clone $eventQuery)->where('starts_at', '>', now())->count();
        $totalTicketsSold = (clone $ticketQuery)->count();
        $totalRevenue = (clone $orderQuery)->where('status', 'paid')->sum('total') ?? 0;
        $totalParticipants = (clone $ticketQuery)->distinct('participant_email')->count('participant_email');
        $totalCheckIns = (clone $checkInQuery)->where('status', 'success')->count();
        $attendanceRate = $totalTicketsSold > 0 ? round(($totalCheckIns / $totalTicketsSold) * 100, 1) : 0;

        return [
            'total_events' => $totalEvents,
            'active_events' => $activeEvents,
            'upcoming_events' => $upcomingEvents,
            'tickets_sold' => $totalTicketsSold,
            'revenue' => (float) $totalRevenue,
            'participants' => $totalParticipants,
            'check_ins' => $totalCheckIns,
            'attendance_rate' => $attendanceRate,
        ];
    }

    public function revenueChart(int $days = 30, ?int $organizationId = null): array
    {
        $query = Order::query()
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->subDays($days));

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        $orders = $query
            ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $result[$date] = 0;
        }

        foreach ($orders as $order) {
            $date = Carbon::parse($order->date)->format('Y-m-d');
            if (isset($result[$date])) {
                $result[$date] = (float) $order->total;
            }
        }

        return $result;
    }

    public function ticketsChart(int $days = 30, ?int $organizationId = null): array
    {
        $query = Ticket::query()
            ->where('created_at', '>=', now()->subDays($days));

        if ($organizationId) {
            $query->whereHas('event', fn ($q) => $q->where('organization_id', $organizationId));
        }

        $tickets = $query
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $result[$date] = 0;
        }

        foreach ($tickets as $ticket) {
            $date = Carbon::parse($ticket->date)->format('Y-m-d');
            if (isset($result[$date])) {
                $result[$date] = $ticket->count;
            }
        }

        return $result;
    }

    public function checkInsByHour(?int $eventId = null): array
    {
        $query = CheckIn::query()
            ->where('status', 'success')
            ->whereDate('checked_in_at', today());

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        $checkIns = $query->get();

        $result = [];
        for ($h = 0; $h < 24; $h++) {
            $result[$h] = 0;
        }

        foreach ($checkIns as $ci) {
            $hour = (int) Carbon::parse($ci->checked_in_at)->format('H');
            $result[$hour] = ($result[$hour] ?? 0) + 1;
        }

        return $result;
    }

    public function ticketsSoldByEvent(?int $organizationId = null, int $limit = 10): array
    {
        $query = Event::query()
            ->withCount('tickets')
            ->orderByDesc('tickets_count');

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        return $query->limit($limit)->get()->map(fn ($e) => [
            'name' => $e->name,
            'count' => $e->tickets_count,
        ])->toArray();
    }

    public function recentActivity(int $limit = 10, ?int $organizationId = null): array
    {
        $query = ActivityLog::query()
            ->with('user')
            ->latest('performed_at');

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        return $query->limit($limit)->get()->toArray();
    }

    public function eventCheckInStats(?int $eventId): array
    {
        if (! $eventId) {
            return [
                'total' => 0,
                'checked_in' => 0,
                'pending' => 0,
                'rate' => 0,
                'by_hour' => [],
            ];
        }

        $event = Event::find($eventId);
        if (! $event) {
            return [];
        }

        $totalTickets = $event->tickets()->count();
        $checkedIn = $event->checkIns()->where('status', 'success')->count();
        $rate = $totalTickets > 0 ? round(($checkedIn / $totalTickets) * 100, 1) : 0;

        return [
            'total' => $totalTickets,
            'checked_in' => $checkedIn,
            'pending' => max(0, $totalTickets - $checkedIn),
            'rate' => $rate,
            'by_hour' => $this->checkInsByHour($eventId),
        ];
    }
}
