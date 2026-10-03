<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Public\CheckoutController;
use App\Http\Controllers\Public\EventPageController;
use App\Http\Controllers\Public\PublicCheckoutController;
use App\Livewire\CheckIn\CheckInScanner;
use App\Livewire\Coupons\CouponTable;
use App\Livewire\Events\EventForm;
use App\Livewire\Events\EventIndex;
use App\Livewire\Events\EventShow;
use App\Livewire\Expenses\ExpenseTable;
use App\Livewire\Orders\OrderTable;
use App\Livewire\Participants\ParticipantTable;
use App\Livewire\Reports\ReportIndex;
use App\Livewire\Reviews\ReviewTable;
use App\Livewire\Schedule\ScheduleIndex;
use App\Livewire\Speakers\SpeakerTable;
use App\Livewire\Sponsors\SponsorTable;
use App\Livewire\Tasks\TaskBoard;
use App\Livewire\Team\TeamIndex;
use App\Livewire\Tickets\TicketTable;
use App\Livewire\Vendors\VendorTable;
use App\Livewire\Waitlist\WaitlistTable;
use App\Models\ActivityLog;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public pages
Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

// Search API
Route::middleware('auth')->get('/api/search', function (SearchService $service, Request $request) {
    $query = $request->get('q', '');
    $orgId = Auth::check() ? Auth::user()->organizations()->first()?->id : null;

    return response()->json($service->globalSearch($query, $orgId));
})->name('api.search');

// Public event pages
Route::get('/event/{slug}', [EventPageController::class, 'show'])->name('public.event.show');
Route::get('/events', [EventPageController::class, 'index'])->name('public.events.index');

// Public checkout flow
Route::prefix('event/{slug}')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('public.checkout');
    Route::post('/checkout/process', [PublicCheckoutController::class, 'process'])->name('public.checkout.process');
});

// Public order success and ticket (no slug needed)
Route::get('/order/{code}/success', [CheckoutController::class, 'success'])->name('public.checkout.success');
Route::get('/ticket/{code}', [CheckoutController::class, 'ticket'])->name('public.ticket');

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Email verification
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
        ->name('verification.send');
});

// Authenticated Dashboard
Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/organization', [SettingsController::class, 'updateOrganization'])->name('settings.organization');

    // Livewire pages
    Route::get('/events', EventIndex::class)->name('events.index');
    Route::get('/events/create', EventForm::class)->name('events.create');
    Route::get('/events/{event}/edit', EventForm::class)->name('events.edit');
    Route::get('/events/{event}', EventShow::class)->name('events.show');

    Route::get('/tickets', TicketTable::class)->name('tickets.index');
    Route::get('/participants', ParticipantTable::class)->name('participants.index');
    Route::get('/check-in', CheckInScanner::class)->name('check-in.index');
    Route::get('/schedule', ScheduleIndex::class)->name('schedule.index');
    Route::get('/speakers', SpeakerTable::class)->name('speakers.index');
    Route::get('/team', TeamIndex::class)->name('team.index');
    Route::get('/vendors', VendorTable::class)->name('vendors.index');
    Route::get('/sponsors', SponsorTable::class)->name('sponsors.index');
    Route::get('/coupons', CouponTable::class)->name('coupons.index');
    Route::get('/reports', ReportIndex::class)->name('reports.index');
    Route::get('/orders', OrderTable::class)->name('orders.index');
    Route::get('/reviews', ReviewTable::class)->name('reviews.index');
    Route::get('/waitlist', WaitlistTable::class)->name('waitlist.index');
    Route::get('/tasks', TaskBoard::class)->name('tasks.index');
    Route::get('/expenses', ExpenseTable::class)->name('expenses.index');
});

// Dashboard page is the main route - we want all authenticated routes
// Note: we override the above with explicit prefix
Route::middleware(['auth', 'verified'])->group(function () {
    // Use dashboard. prefix on name
});

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    // Notifications
    Route::get('/notifications', function () {
        return view('dashboard.notifications', [
            'title' => 'Notificações',
            'notifications' => auth()->user()->notifications()->latest()->paginate(20),
        ]);
    })->name('dashboard.notifications.index');

    Route::get('/activity', function () {
        return view('dashboard.activity', [
            'title' => 'Atividade',
            'logs' => ActivityLog::with('user')
                ->where('organization_id', auth()->user()->organizations()->first()?->id)
                ->latest('performed_at')
                ->paginate(30),
        ]);
    })->name('dashboard.activity.index');
});
