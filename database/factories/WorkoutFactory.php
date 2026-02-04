<?php

namespace Database\Factories;

use App\Models\Phase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workout>
 */
class WorkoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $phase = Phase::inRandomOrder()->first();

        $workoutTypes = [
            'Силовая тренировка на верх тела',
            'Тренировка ног и ягодиц',
            'Кардио сессия для сжигания жира',
            'HIIT тренировка на все тело',
            'Круговая тренировка на выносливость',
            'Тренировка на пресс и кор',
            'Функциональный тренинг',
            'Тренировка на силу и мощь',
            'Йога для гибкости и релаксации',
            'Тренировка для реабилитации'
        ];
        return [
            'phase_id' => $phase->id,
            'title' => fake()->randomElement($workoutTypes),
            'description' => fake()->paragraph(3),
            'duration_minutes' => fake()->numberBetween(20, 120),
            'is_active' => fake()->boolean(90),
        ];
    }

    public function short(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_minutes' => fake()->numberBetween(20, 40),
        ]);
    }

    public function medium(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_minutes' => fake()->numberBetween(45, 60),
        ]);
    }

    public function long(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_minutes' => fake()->numberBetween(75, 120),
        ]);
    }

    public function forBeginners(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Тренировка для начинающих',
            'duration_minutes' => fake()->numberBetween(20, 30),
        ]);
    }

    public function forAdvanced(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Продвинутая тренировка',
            'duration_minutes' => fake()->numberBetween(60, 90),
        ]);
    }
}
