<?php

namespace Database\Factories;
use App\Models\Testing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TestingExercise>
 */
class TestingExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'testing_id' => Testing::factory(),
            'description' => fake()->paragraph(2),
            'image' => 'testing_exercises/' . fake()->image(null, 600, 400, 'exercise', true),
        ];
    }
}
