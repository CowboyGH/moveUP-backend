<?php

namespace Database\Factories;
use App\Models\Exercise;
use App\Models\Testing;

use Illuminate\Database\Eloquent\Factories\Factory;

class TestingExerciseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'exercise_id' => Exercise::inRandomOrder()->first()?->id
                ?? Exercise::factory()->create()->id,
            'description' => fake()->paragraph(2),
            'image' => 'testing_exercises/' . fake()->image(null, 600, 400, 'exercise', true),
        ];
    }
}
