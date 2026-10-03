<?php

namespace Database\Factories;

use App\Enums\VendorCategory;
use App\Models\Organization;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vendor>
 */
class VendorFactory extends Factory
{
    public function definition(): array
    {
        $categories = VendorCategory::cases();

        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->name(),
            'company_name' => fake()->company(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->numerify('+55 ## ####-####'),
            'document' => fake()->numerify('##.###.###/####-##'),
            'category' => fake()->randomElement($categories),
            'cost' => fake()->randomFloat(2, 500, 5000),
            'address' => fake()->streetAddress(),
            'notes' => fake()->optional(0.5)->sentence(),
            'status' => 'active',
        ];
    }
}
