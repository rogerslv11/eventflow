<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(4),
            'email' => fake()->companyEmail(),
            'phone' => fake()->numerify('+55 ## ####-####'),
            'document' => fake()->numerify('##.###.###/####-##'),
            'address' => fake()->streetAddress().', '.fake()->numberBetween(1, 2000).' - '.fake()->city(),
            'primary_color' => fake()->randomElement(['#7C3AED', '#3B82F6', '#10B981', '#F59E0B', '#EF4444']),
            'is_active' => true,
        ];
    }
}
