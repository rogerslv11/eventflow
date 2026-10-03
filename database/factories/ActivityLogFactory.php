<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    public function definition(): array
    {
        $actions = ['created', 'updated', 'deleted', 'login', 'logout', 'check_in', 'purchase'];

        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'action' => fake()->randomElement($actions),
            'entity_type' => fake()->randomElement(['App\\Models\\Event', 'App\\Models\\Ticket', 'App\\Models\\Order']),
            'entity_id' => fake()->numberBetween(1, 100),
            'description' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'performed_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
