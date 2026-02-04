<?php

namespace Database\Factories;

use App\Models\Warmup;
use App\Models\Workout;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutWarmup>
 */
class WorkoutWarmupFactory extends Factory
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
            'warmup_id' => Warmup::factory(),
            'order_number' => fake()->numberBetween(1, 5),
        ];
    }
}
