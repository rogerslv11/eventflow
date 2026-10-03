<?php

namespace Database\Factories;

use App\Enums\TicketType as TicketTypeEnum;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TicketType>
 */
class TicketTypeFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement([
            TicketTypeEnum::Paid, TicketTypeEnum::Vip, TicketTypeEnum::Premium, TicketTypeEnum::Free, TicketTypeEnum::Courtesy,
        ]);

        $name = match ($type) {
            TicketTypeEnum::Free => 'Entrada Gratuita',
            TicketTypeEnum::Vip => 'Ingresso VIP',
            TicketTypeEnum::Premium => 'Ingresso Premium',
            TicketTypeEnum::Courtesy => 'Ingresso Cortesia',
            TicketTypeEnum::Press => 'Ingresso Imprensa',
            default => 'Ingresso '.fake()->randomElement(['Standard', 'Regular', 'Básico', 'Promocional']),
        };

        $price = match ($type) {
            TicketTypeEnum::Free, TicketTypeEnum::Courtesy => 0,
            TicketTypeEnum::Paid => fake()->randomFloat(2, 50, 200),
            TicketTypeEnum::Vip => fake()->randomFloat(2, 300, 800),
            TicketTypeEnum::Premium => fake()->randomFloat(2, 800, 2000),
            TicketTypeEnum::Press => 0,
        };

        return [
            'event_id' => Event::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(3),
            'description' => fake()->sentence(),
            'type' => $type,
            'price' => $price,
            'quantity' => fake()->numberBetween(50, 1000),
            'sold' => fake()->numberBetween(0, 50),
            'reserved' => 0,
            'min_per_order' => 1,
            'max_per_order' => 10,
            'sales_start_at' => now()->subDays(30),
            'sales_end_at' => now()->addDays(60),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 10),
            'benefits' => match ($type) {
                TicketTypeEnum::Vip => ['Acesso à área VIP', 'Welcome drink', 'Brindes exclusivos'],
                TicketTypeEnum::Premium => ['Acesso premium', 'Coffe break', 'Material exclusivo'],
                default => null,
            },
        ];
    }

    public function free(): static
    {
        return $this->state(fn () => [
            'type' => TicketTypeEnum::Free,
            'price' => 0,
        ]);
    }

    public function vip(): static
    {
        return $this->state(fn () => [
            'type' => TicketTypeEnum::Vip,
            'price' => fake()->randomFloat(2, 300, 800),
        ]);
    }
}
