<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PhaseFactory extends Factory
{
    public function definition(): array
    {
        $phases = [
            'Подготовительная фаза',
            'Базовая фаза',
            'Интенсивная фаза',
            'Фаза отдыха'
        ];

        return [
            'name' => fake()->randomElement($phases),
            'description' => fake()->paragraph(2),
            'duration_days' => fake()->numberBetween(7, 28),
            'order_number' => fake()->numberBetween(1, 6),
        ];
    }

    public function preparation(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Подготовительная фаза',
            'order_number' => 1,
            'duration_days' => 7,
        ]);
    }

    public function basic(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Базовая фаза',
            'order_number' => 2,
            'duration_days' => 14,
        ]);
    }

    public function intense(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Интенсивная фаза',
            'order_number' => 3,
            'duration_days' => 21,
        ]);
    }

    public function rest(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Фаза отдыха',
            'order_number' => 6,
            'duration_days' => 7,
        ]);
    }
}
