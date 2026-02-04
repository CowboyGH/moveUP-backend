<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GoalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Рост силовых показателей',
                'Рост мышечной массы',
                'Жиросжигание',
                'Общее укрепление организма',
            ]),
        ];
    }

    public function strengthGrowth(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Рост силовых показателей',
        ]);
    }

    public function muscleMass(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Рост мышечной массы',
        ]);
    }

    public function fatLoss(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Жиросжигание',
        ]);
    }

    public function generalHealth(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Общее укрепление организма',
        ]);
    }
}
