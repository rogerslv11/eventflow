<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Services\QrCodeService;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(string $slug): View
    {
        $event = Event::with(['ticketTypes', 'organization'])
            ->where('slug', $slug)
            ->whereIn('status', ['published', 'active'])
            ->firstOrFail();

        return view('public.checkout', [
            'event' => $event,
            'title' => "Comprar ingresso - {$event->name}",
        ]);
    }

    public function success(string $code): View
    {
        $order = Order::with(['event', 'items.ticketType', 'tickets'])
            ->where('code', $code)
            ->firstOrFail();

        $qrService = app(QrCodeService::class);

        return view('public.checkout-success', [
            'order' => $order,
            'qrService' => $qrService,
            'title' => 'Compra confirmada!',
        ]);
    }

    public function ticket(string $code): View
    {
        $ticket = Ticket::with(['event', 'ticketType'])
            ->where('code', $code)
            ->firstOrFail();

        $qrService = app(QrCodeService::class);

        return view('public.ticket', [
            'ticket' => $ticket,
            'qrService' => $qrService,
            'title' => 'Seu ingresso',
        ]);
    }
}
