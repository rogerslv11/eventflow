<?php

namespace App\Jobs;

use App\Mail\TicketPurchaseMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTicketEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $orderId) {}

    public function handle(): void
    {
        $order = Order::with(['event', 'items.ticketType', 'tickets'])->find($this->orderId);
        if (! $order) {
            return;
        }

        // In a real app, send email. In dev, just log it.
        Mail::to($order->customer_email)->send(new TicketPurchaseMail($order));
    }
}
