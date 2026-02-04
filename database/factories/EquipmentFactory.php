<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    public function definition(): array
    {
        $equipment = [
            'Смешанное',
            'Зал'
        ];

        return [
            'name' => fake()->unique()->randomElement($equipment),
        ];
    }

    public function gym(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Зал',
        ]);
    }

    public function mixed(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Смешанное',
        ]);
    }
}
