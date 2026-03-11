<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LevelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Начинающий',
                'Опытный',
                'Продвинутый'
            ]),
        ];
    }

    public function beginner(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Начинающий',
        ]);
    }

    public function intermediate(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Опытный',
        ]);
    }

    public function advanced(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Продвинутый',
        ]);
    }
}
