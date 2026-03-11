<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Exercise;
use App\Models\UserWorkout;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseReactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'         => User::factory(),
            'exercise_id'     => Exercise::factory(),
            'user_workout_id' => UserWorkout::factory(),
            'reaction'        => $this->faker->randomElement(\App\Models\ExerciseReaction::getReactions()),
            'reaction_date'   => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }

    public function good(): static
    {
        return $this->state(fn (array $attributes) => [
            'reaction' => \App\Models\ExerciseReaction::REACTION_GOOD,
        ]);
    }

    public function normal(): static
    {
        return $this->state(fn (array $attributes) => [
            'reaction' => \App\Models\ExerciseReaction::REACTION_NORMAL,
        ]);
    }

    public function bad(): static
    {
        return $this->state(fn (array $attributes) => [
            'reaction' => \App\Models\ExerciseReaction::REACTION_BAD,
        ]);
    }

    public function onDate($date): static
    {
        return $this->state(fn (array $attributes) => [
            'reaction_date' => $date,
        ]);
    }
}
