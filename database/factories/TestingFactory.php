<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Testing>
 */
class TestingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(4, true),
            'description' => fake()->paragraph(3),
            'duration_minutes' => fake()->numberBetween(5, 60),
            'image' => 'tests/' . fake()->image(null, 800, 600, 'sports', true),
            'is_active' => fake()->boolean(80),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function short(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_minutes' => fake()->numberBetween(5, 15),
        ]);
    }

    public function long(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_minutes' => fake()->numberBetween(30, 60),
        ]);
    }
}
