<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Tecnologia', 'Marketing', 'Educação', 'Saúde', 'Negócios',
            'Entretenimento', 'Música', 'Esportes', 'Gastronomia', 'Moda',
        ]);

        return [
            'organization_id' => Organization::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'color' => fake()->randomElement(['#7C3AED', '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#EC4899']),
            'icon' => fake()->randomElement(['calendar', 'briefcase', 'music', 'trophy', 'cpu', 'book']),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
