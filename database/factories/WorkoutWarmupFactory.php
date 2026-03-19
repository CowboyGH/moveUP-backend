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
            'workout_id' => Workout::inRandomOrder()->first()?->id ?? Workout::factory(),
            'warmup_id' => Warmup::inRandomOrder()->first()?->id ?? Warmup::factory(),
            'order_number' => $this->faker->numberBetween(1, 5),
        ];
    }
}
