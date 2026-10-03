<?php

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Models\Category;
use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Summit de Tecnologia', 'Congresso de Marketing Digital', 'Festival de Inovação',
            'Workshop de Liderança', 'Conferência de IA', 'Expo de Negócios',
            'Meetup de Desenvolvedores', 'Encontro de Empreendedores', 'Feira de Design',
            'Semana de Tecnologia', 'Hackathon de Inovação', 'Palestra sobre Marketing',
        ]).' '.fake()->year();

        $startsAt = fake()->dateTimeBetween('-60 days', '+90 days');

        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(4),
            'description' => fake()->paragraph(),
            'content' => fake()->paragraphs(3, true),
            'venue_name' => fake()->randomElement(['Centro de Convenções', 'Hotel Premium', 'Auditório Principal', 'Espaço Innovation', 'Pavilhão de Eventos']),
            'address' => fake()->streetAddress(),
            'city' => fake()->randomElement(['São Paulo', 'Rio de Janeiro', 'Belo Horizonte', 'Curitiba', 'Porto Alegre', 'Recife', 'Salvador', 'Fortaleza', 'Brasília', 'Florianópolis']),
            'state' => fake()->randomElement(['SP', 'RJ', 'MG', 'PR', 'RS', 'PE', 'BA', 'CE', 'DF', 'SC']),
            'zipcode' => fake()->numerify('#####-###'),
            'latitude' => fake()->latitude(-30, -5),
            'longitude' => fake()->longitude(-60, -35),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+8 hours'),
            'timezone' => 'America/Sao_Paulo',
            'capacity' => fake()->numberBetween(100, 5000),
            'status' => EventStatus::Published,
            'is_featured' => fake()->boolean(30),
            'is_public' => true,
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => EventStatus::Draft]);
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => EventStatus::Active,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHours(4),
        ]);
    }

    public function finished(): static
    {
        return $this->state(fn () => [
            'status' => EventStatus::Finished,
            'starts_at' => now()->subDays(30),
            'ends_at' => now()->subDays(29),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
