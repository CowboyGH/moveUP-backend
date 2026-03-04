<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserExerciseWeight>
 */
class UserExerciseWeightFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $weight = $this->faker->randomFloat(1, 5, 200); // вес от 5 до 200 кг с одним знаком

        return [
            'user_id'           => User::factory(),
            'exercise_id'       => Exercise::factory(),
            'weight'            => \App\Models\UserExerciseWeight::roundWeight($weight),
            'adjustment_factor' => $this->faker->randomFloat(2, 0.8, 1.2),
        ];
    }

    /**
     * Указать конкретный вес
     */
    public function withWeight(float $weight): static
    {
        return $this->state(fn (array $attributes) => [
            'weight' => \App\Models\UserExerciseWeight::roundWeight($weight),
        ]);
    }

    /**
     * Указать коэффициент корректировки
     */
    public function withAdjustmentFactor(float $factor): static
    {
        return $this->state(fn (array $attributes) => [
            'adjustment_factor' => $factor,
        ]);
    }
}
