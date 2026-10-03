<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Schedule;
use App\Models\Speaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-15 days', '+30 days');
        $duration = fake()->randomElement([30, 45, 60, 90, 120]);
        $endsAt = (clone $startsAt)->modify("+{$duration} minutes");

        $types = ['talk', 'workshop', 'panel', 'break', 'opening', 'closing'];

        return [
            'event_id' => Event::factory(),
            'speaker_id' => Speaker::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'type' => fake()->randomElement($types),
            'room' => fake()->randomElement(['Auditório Principal', 'Sala A', 'Sala B', 'Sala C', 'Workshop Room 1']),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'is_featured' => fake()->boolean(20),
        ];
    }
}
