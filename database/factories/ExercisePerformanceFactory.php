<?php

namespace Database\Factories;

use App\Models\UserWorkout;
use App\Models\Exercise;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExercisePerformance>
 */
class ExercisePerformanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_workout_id' => UserWorkout::factory(),
            'exercise_id' => Exercise::factory(),
            'reaction' => $this->faker->randomElement(['bad', 'normally', 'good']),
        ];
    }

    public function badReaction(): self
    {
        return $this->state(['reaction' => 'bad']);
    }

    public function normalReaction(): self
    {
        return $this->state(['reaction' => 'normally']);
    }

    public function goodReaction(): self
    {
        return $this->state(['reaction' => 'good']);
    }
}
