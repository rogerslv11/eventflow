<?php

namespace Database\Factories;

use App\Enums\CouponType;
use App\Models\Coupon;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement([CouponType::Percentage, CouponType::Fixed]);
        $value = match ($type) {
            CouponType::Percentage => fake()->randomFloat(2, 5, 50),
            CouponType::Fixed => fake()->randomFloat(2, 10, 100),
        };

        return [
            'event_id' => Event::factory(),
            'organization_id' => Organization::factory(),
            'code' => strtoupper(fake()->unique()->bothify('EVENTFLOW####')),
            'name' => "Cupom {$value}".($type === CouponType::Percentage ? '%' : ' reais'),
            'description' => fake()->sentence(),
            'type' => $type,
            'value' => $value,
            'usage_limit' => fake()->numberBetween(10, 500),
            'usage_count' => fake()->numberBetween(0, 50),
            'usage_limit_per_user' => 1,
            'min_purchase' => fake()->randomFloat(2, 50, 200),
            'max_discount' => $type === CouponType::Percentage ? fake()->randomFloat(2, 50, 200) : null,
            'starts_at' => now()->subDays(15),
            'ends_at' => now()->addDays(45),
            'is_active' => true,
        ];
    }
}
