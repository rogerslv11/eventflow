<?php

namespace App\Livewire\Dashboard;

use App\Services\StatsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
#[Layout('layouts.app')]
class DashboardStats extends Component
{
    public function render()
    {
        $user = Auth::user();
        $organizationId = $user->organizations()->first()?->id;

        $statsService = app(StatsService::class);
        $stats = $statsService->dashboardStats($organizationId);
        $revenueChart = $statsService->revenueChart(30, $organizationId);
        $ticketsChart = $statsService->ticketsChart(30, $organizationId);
        $topEvents = $statsService->ticketsSoldByEvent($organizationId, 5);
        $recentActivity = $statsService->recentActivity(8, $organizationId);

        return view('livewire.dashboard.dashboard-stats', [
            'stats' => $stats,
            'revenueChart' => $revenueChart,
            'ticketsChart' => $ticketsChart,
            'topEvents' => $topEvents,
            'recentActivity' => $recentActivity,
            'organization' => $user->organizations()->first(),
        ]);
    }
}
