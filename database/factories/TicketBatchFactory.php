<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\TicketBatch;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketBatch>
 */
class TicketBatchFactory extends Factory
{
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 50, 500);

        return [
            'ticket_type_id' => TicketType::factory(),
            'event_id' => Event::factory(),
            'name' => fake()->randomElement(['1º Lote', '2º Lote', '3º Lote', 'Lote Final', 'Lote Promocional']),
            'price' => $price,
            'quantity' => fake()->numberBetween(50, 500),
            'sold' => 0,
            'starts_at' => now()->subDays(30),
            'ends_at' => now()->addDays(60),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 5),
        ];
    }
}
