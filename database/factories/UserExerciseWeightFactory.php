<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Exercise;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserExerciseWeightFactory extends Factory
{
    public function definition(): array
    {
        $weight = $this->faker->randomFloat(1, 5, 200);

        return [
            'user_id'           => User::factory(),
            'exercise_id'       => Exercise::factory(),
            'weight'            => \App\Models\UserExerciseWeight::roundWeight($weight),
            'adjustment_factor' => $this->faker->randomFloat(2, 0.8, 1.2),
        ];
    }

    public function withWeight(float $weight): static
    {
        return $this->state(fn (array $attributes) => [
            'weight' => \App\Models\UserExerciseWeight::roundWeight($weight),
        ]);
    }

    public function withAdjustmentFactor(float $factor): static
    {
        return $this->state(fn (array $attributes) => [
            'adjustment_factor' => $factor,
        ]);
    }
}
