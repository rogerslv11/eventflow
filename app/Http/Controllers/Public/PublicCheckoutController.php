<?php

namespace App\Http\Controllers\Public;

use App\Models\Event;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class PublicCheckoutController
{
    public function process(Request $request, string $slug)
    {
        $event = Event::with('ticketTypes')
            ->where('slug', $slug)
            ->whereIn('status', ['published', 'active'])
            ->firstOrFail();

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'items.*.quantity' => 'required|integer|min:1|max:10',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'document' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        $service = app(CheckoutService::class);

        try {
            $order = $service->createOrder(
                event: $event,
                items: $validated['items'],
                customer: [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'document' => $validated['document'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'company' => $validated['company'] ?? null,
                    'role' => $validated['role'] ?? null,
                ],
                couponCode: $validated['coupon_code'] ?? null,
            );

            // Process payment (mocked - always succeeds)
            $order = $service->processPayment($order, 'credit_card');

            return response()->json([
                'success' => true,
                'redirect' => route('public.checkout.success', $order->code),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
