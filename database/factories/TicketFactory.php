<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => 'TKT-'.strtoupper(Str::random(12)),
            'event_id' => Event::factory(),
            'ticket_type_id' => TicketType::factory(),
            'participant_name' => fake()->name(),
            'participant_email' => fake()->safeEmail(),
            'participant_document' => fake()->numerify('###.###.###-##'),
            'participant_phone' => fake()->numerify('+55 ## 9####-####'),
            'participant_company' => fake()->optional(0.5)->company(),
            'participant_role' => fake()->optional(0.5)->jobTitle(),
            'price_paid' => fake()->randomFloat(2, 50, 500),
            'status' => TicketStatus::Active,
            'qr_code' => Str::uuid()->toString(),
        ];
    }
}
