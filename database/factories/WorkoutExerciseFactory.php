<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\Workout;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutExercise>
 */
class WorkoutExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workout_id' => Workout::factory(),
            'exercise_id' => Exercise::factory(),
            'sets' => fake()->numberBetween(2, 5),
            'reps' => fake()->numberBetween(5, 20),
            'order_number' => fake()->numberBetween(1, 10),
        ];
    }

    public function strength(): static
    {
        return $this->state(fn (array $attributes) => [
            'sets' => fake()->numberBetween(3, 5),
            'reps' => fake()->numberBetween(4, 8),
        ]);
    }

    public function hypertrophy(): static
    {
        return $this->state(fn (array $attributes) => [
            'sets' => fake()->numberBetween(3, 4),
            'reps' => fake()->numberBetween(8, 12),
        ]);
    }

    public function endurance(): static
    {
        return $this->state(fn (array $attributes) => [
            'sets' => fake()->numberBetween(2, 3),
            'reps' => fake()->numberBetween(15, 20),
        ]);
    }
}
