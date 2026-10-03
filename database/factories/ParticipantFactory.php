<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Participant>
 */
class ParticipantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'document' => fake()->numerify('###.###.###-##'),
            'phone' => fake()->numerify('+55 ## 9####-####'),
            'company' => fake()->optional(0.6)->company(),
            'role' => fake()->optional(0.6)->jobTitle(),
            'city' => fake()->randomElement(['São Paulo', 'Rio de Janeiro', 'Belo Horizonte', 'Curitiba', 'Porto Alegre']),
            'state' => fake()->randomElement(['SP', 'RJ', 'MG', 'PR', 'RS']),
            'status' => 'confirmed',
            'has_checked_in' => false,
        ];
    }
}
