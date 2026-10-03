<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketPurchaseMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Seu ingresso para {$this->order->event->name} - EventFlow",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-purchase',
            with: [
                'order' => $this->order,
                'event' => $this->order->event,
            ],
        );
    }
}
