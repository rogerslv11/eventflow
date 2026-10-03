<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50, 1000);
        $discount = fake()->boolean(20) ? fake()->randomFloat(2, 10, 50) : 0;
        $total = $subtotal - $discount;

        return [
            'code' => 'ORD-'.strtoupper(Str::random(10)),
            'event_id' => Event::factory(),
            'organization_id' => Organization::factory(),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_document' => fake()->numerify('###.###.###-##'),
            'customer_phone' => fake()->numerify('+55 ## 9####-####'),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'fee' => fake()->randomFloat(2, 2, 10),
            'status' => fake()->randomElement([
                OrderStatus::Paid, OrderStatus::Paid, OrderStatus::Paid,
                OrderStatus::Pending, OrderStatus::Cancelled,
            ]),
            'payment_method' => fake()->randomElement(['credit_card', 'pix', 'boleto']),
            'paid_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-30 days', 'now') : null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::Paid,
            'paid_at' => now(),
        ]);
    }
}
