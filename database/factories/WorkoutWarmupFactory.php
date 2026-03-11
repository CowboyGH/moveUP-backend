<?php

namespace Database\Factories;

use App\Models\Warmup;
use App\Models\Workout;

use Illuminate\Database\Eloquent\Factories\Factory;

class WorkoutWarmupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workout_id' => Workout::factory(),
            'warmup_id' => Warmup::factory(),
            'order_number' => fake()->numberBetween(1, 5),
        ];
    }
}
