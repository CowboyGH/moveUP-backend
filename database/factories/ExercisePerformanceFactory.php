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
        // Генерируем плановые показатели
        $setsPlanned = $this->faker->numberBetween(3, 5);
        $repsPlanned = $this->faker->numberBetween(8, 15);
        $weightPlanned = $this->faker->randomFloat(1, 20, 150); // вес с одним знаком после запятой

        // Фактическое выполнение может немного отличаться
        $setsCompleted = $this->faker->optional(0.9, $setsPlanned)->numberBetween($setsPlanned - 1, $setsPlanned);
        $repsCompleted = $this->faker->optional(0.9, $repsPlanned)->numberBetween($repsPlanned - 3, $repsPlanned + 2);
        $weightUsed = $this->faker->optional(0.8, $weightPlanned)->randomFloat(1, $weightPlanned * 0.9, $weightPlanned * 1.1);

        return [
            'user_workout_id'   => UserWorkout::factory(),
            'exercise_id'       => Exercise::factory(),
            'reaction'          => $this->faker->randomElement(['bad', 'normal', 'good']),
            'sets_completed'    => max(0, $setsCompleted),
            'reps_completed'    => max(0, $repsCompleted),
            'weight_used'       => round($weightUsed, 1),
            'sets_planned'      => $setsPlanned,
            'reps_planned'      => $repsPlanned,
            'weight_planned'    => $weightPlanned,
            'adjustment_factor' => $this->faker->randomFloat(2, 0.8, 1.2),
        ];
    }

    /**
     * Состояние для отрицательной реакции
     */
    public function badReaction(): static
    {
        return $this->state(fn (array $attributes) => [
            'reaction' => 'bad',
        ]);
    }

    /**
     * Состояние для нормальной реакции
     */
    public function normalReaction(): static
    {
        return $this->state(fn (array $attributes) => [
            'reaction' => 'normal',
        ]);
    }

    /**
     * Состояние для хорошей реакции
     */
    public function goodReaction(): static
    {
        return $this->state(fn (array $attributes) => [
            'reaction' => 'good',
        ]);
    }
}
