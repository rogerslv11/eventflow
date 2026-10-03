<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Speaker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Speaker>
 */
class SpeakerFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->name();

        return [
            'organization_id' => Organization::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(4),
            'bio' => fake()->paragraph(),
            'role' => fake()->randomElement(['CEO', 'CTO', 'Diretor', 'Gerente', 'Especialista', 'Palestrante']),
            'company' => fake()->company(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('+55 ## 9####-####'),
            'website' => fake()->url(),
            'social_links' => [
                'linkedin' => 'https://linkedin.com/in/'.Str::slug($name),
                'twitter' => 'https://twitter.com/'.Str::slug($name),
            ],
            'is_featured' => fake()->boolean(30),
        ];
    }
}
