<?php

namespace Database\Factories;

use App\Models\Phase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProgressFactory extends Factory
{
    public function definition(): array
    {
        $phase = Phase::inRandomOrder()->first();
        return [
            'user_id' => User::factory(),
            'phase_id' => $phase->id,
            'streak_days' => fake()->numberBetween(1, 365),
        ];
    }

    public function newUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'streak_days' => fake()->numberBetween(1, 7),
        ]);
    }

    public function activeUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'streak_days' => fake()->numberBetween(30, 90),
        ]);
    }
    public function longActiveUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'streak_days' => fake()->numberBetween(100, 365),
        ]);
    }
}
