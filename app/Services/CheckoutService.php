<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Models\Coupon;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        protected QrCodeService $qrCode,
    ) {}

    /**
     * @param  array<int, array{ticket_type_id:int, quantity:int}>  $items
     * @param  array{name:string, email:string, document?:string, phone?:string, company?:string, role?:string}  $customer
     */
    public function createOrder(
        Event $event,
        array $items,
        array $customer,
        ?string $couponCode = null,
        array $participants = [],
    ): Order {
        return DB::transaction(function () use ($event, $items, $customer, $couponCode) {
            $coupon = null;
            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)
                    ->where(function ($q) use ($event) {
                        $q->whereNull('event_id')->orWhere('event_id', $event->id);
                    })
                    ->first();
            }

            $subtotal = 0;
            $orderItems = [];

            foreach ($items as $item) {
                $ticketType = $event->ticketTypes()->findOrFail($item['ticket_type_id']);
                $quantity = (int) ($item['quantity'] ?? 1);

                if (! $ticketType->isOnSale() || $ticketType->available() < $quantity) {
                    throw new \RuntimeException("Ingresso {$ticketType->name} indisponível ou esgotado.");
                }

                $unitPrice = (float) $ticketType->price;
                $itemSubtotal = $unitPrice * $quantity;
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'ticket_type_id' => $ticketType->id,
                    'ticket_batch_id' => $ticketType->getCurrentBatch()?->id,
                    'name' => $ticketType->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'subtotal' => $itemSubtotal,
                    'discount' => 0,
                    'total' => $itemSubtotal,
                ];
            }

            $discount = 0;
            if ($coupon && $coupon->isValid()) {
                if (! $coupon->min_purchase || $subtotal >= (float) $coupon->min_purchase) {
                    $discount = $coupon->calculateDiscount($subtotal);
                }
            }

            $total = max(0, $subtotal - $discount);

            $order = Order::create([
                'code' => 'ORD-'.strtoupper(Str::random(10)),
                'event_id' => $event->id,
                'organization_id' => $event->organization_id,
                'customer_name' => $customer['name'],
                'customer_email' => $customer['email'],
                'customer_document' => $customer['document'] ?? null,
                'customer_phone' => $customer['phone'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'status' => OrderStatus::Pending,
                'coupon_id' => $coupon?->id,
                'expires_at' => now()->addMinutes(30),
            ]);

            foreach ($orderItems as $oi) {
                $order->items()->create($oi);
            }

            // Reserve ticket types
            foreach ($items as $item) {
                $ticketType = $event->ticketTypes()->find($item['ticket_type_id']);
                if ($ticketType) {
                    $ticketType->increment('reserved', $item['quantity']);
                }
            }

            return $order;
        });
    }

    public function processPayment(Order $order, string $paymentMethod = 'credit_card'): Order
    {
        return DB::transaction(function () use ($order, $paymentMethod) {
            // Mock payment processing - always succeeds in dev
            $order->update([
                'status' => OrderStatus::Paid,
                'payment_method' => $paymentMethod,
                'payment_id' => 'MOCK-'.strtoupper(Str::random(8)),
                'paid_at' => now(),
            ]);

            // Update reserved to sold
            foreach ($order->items()->get() as $item) {
                $ticketType = $item->ticketType;
                $ticketType->decrement('reserved', $item->quantity);
                $ticketType->increment('sold', $item->quantity);

                // Update batch sales
                if ($item->ticket_batch_id) {
                    TicketBatch::where('id', $item->ticket_batch_id)
                        ->increment('sold', $item->quantity);
                }

                // Create tickets for the order
                for ($i = 0; $i < $item->quantity; $i++) {
                    $ticket = Ticket::create([
                        'code' => 'TKT-'.strtoupper(Str::random(12)),
                        'event_id' => $order->event_id,
                        'order_id' => $order->id,
                        'ticket_type_id' => $ticketType->id,
                        'ticket_batch_id' => $item->ticket_batch_id,
                        'participant_name' => $order->customer_name,
                        'participant_email' => $order->customer_email,
                        'participant_document' => $order->customer_document,
                        'participant_phone' => $order->customer_phone,
                        'price_paid' => $item->unit_price,
                        'status' => TicketStatus::Active,
                        'qr_code' => Str::uuid()->toString(),
                    ]);
                }
            }

            // Update event tickets sold
            $order->event->increment('tickets_sold', $order->items()->sum('quantity'));

            // Track coupon usage
            if ($order->coupon_id) {
                $order->coupon->increment('usage_count');
                $order->coupon->usages()->create([
                    'order_id' => $order->id,
                    'discount_applied' => $order->discount,
                    'customer_email' => $order->customer_email,
                ]);
            }

            return $order->fresh();
        });
    }

    public function cancelOrder(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order->update([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
            ]);

            // Release reserved tickets
            foreach ($order->items()->get() as $item) {
                $ticketType = $item->ticketType;
                if ($order->status === OrderStatus::Pending) {
                    $ticketType->decrement('reserved', $item->quantity);
                } else {
                    $ticketType->decrement('sold', $item->quantity);
                }
            }

            return $order->fresh();
        });
    }
}
