<?php

namespace Database\Factories;

use App\Enums\CheckInStatus;
use App\Models\CheckIn;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CheckIn>
 */
class CheckInFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'ticket_id' => Ticket::factory(),
            'status' => CheckInStatus::Success,
            'gate' => fake()->randomElement(['Portão A', 'Portão B', 'Portão C', 'VIP']),
            'location' => fake()->randomElement(['Entrada Principal', 'Lateral Esquerda', 'Lateral Direita']),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'checked_in_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ];
    }
}
