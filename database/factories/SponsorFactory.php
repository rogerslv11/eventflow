<?php

namespace Database\Factories;

use App\Enums\SponsorTier;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Sponsor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sponsor>
 */
class SponsorFactory extends Factory
{
    public function definition(): array
    {
        $tier = fake()->randomElement([
            SponsorTier::Master, SponsorTier::Gold, SponsorTier::Silver, SponsorTier::Bronze, SponsorTier::Support,
        ]);

        return [
            'event_id' => Event::factory(),
            'organization_id' => Organization::factory(),
            'name' => fake()->company(),
            'website' => fake()->url(),
            'description' => fake()->sentence(),
            'tier' => $tier,
            'contribution' => match ($tier) {
                SponsorTier::Master => fake()->randomFloat(2, 50000, 200000),
                SponsorTier::Gold => fake()->randomFloat(2, 20000, 50000),
                SponsorTier::Silver => fake()->randomFloat(2, 10000, 20000),
                SponsorTier::Bronze => fake()->randomFloat(2, 5000, 10000),
                SponsorTier::Support => fake()->randomFloat(2, 1000, 5000),
            },
            'sort_order' => $tier->sortOrder(),
            'is_active' => true,
        ];
    }
}
