<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkoutExerciseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workout_id' => Workout::inRandomOrder()->first()?->id ?? Workout::factory(),
            'exercise_id' => Exercise::inRandomOrder()->first()?->id ?? Exercise::factory(),
            'sets' => $this->faker->numberBetween(2, 5),
            'reps' => $this->faker->numberBetween(5, 20),
            'order_number' => $this->faker->numberBetween(1, 10),
        ];
    }
}
