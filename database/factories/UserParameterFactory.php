<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\Goal;
use App\Models\Level;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserParameterFactory extends Factory
{
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);

        $height = $gender === 'male'
            ? fake()->numberBetween(165, 200)
            : fake()->numberBetween(150, 180);

        $weight = $gender === 'male'
            ? fake()->numberBetween(65, 110)
            : fake()->numberBetween(45, 80);

        $age = fake()->numberBetween(18, 60);

        $levels = ['Начинающий', 'Средний', 'Продвинутый'];
        $goals = ['Рост силовых показателей', 'Рост мышечной массы', 'Жиросжигание', 'Общее укрепление организма',];
        $equipmentTypes = ['Гантели', 'Штанга', 'Тренажеры', 'Собственный вес', 'Смешанное'];

        return [
            'user_id' => User::factory(),
            'equipment_id' => Equipment::whereIn('name', $equipmentTypes)->inRandomOrder()->first()?->id
                ?? Equipment::factory()->create(['name' => fake()->randomElement($equipmentTypes)])->id,
            'level_id' => Level::whereIn('name', $levels)->inRandomOrder()->first()?->id
                ?? Level::factory()->create(['name' => fake()->randomElement($levels)])->id,
            'goal_id' => Goal::whereIn('name', $goals)->inRandomOrder()->first()?->id
                ?? Goal::factory()->create(['name' => fake()->randomElement($goals)])->id,
            'height' => $height,
            'weight' => $weight,
            'age' => $age,
            'gender' => $gender,
        ];
    }

    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'male',
            'height' => fake()->numberBetween(165, 200),
            'weight' => fake()->numberBetween(65, 110),
        ]);
    }

    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'female',
            'height' => fake()->numberBetween(150, 180),
            'weight' => fake()->numberBetween(45, 80),
        ]);
    }

    public function beginner(): static
    {
        return $this->state(fn (array $attributes) => [
            'level_id' => Level::where('name', 'Начинающий')->first()->id
                ?? Level::factory()->create(['name' => 'Начинающий'])->id,
        ]);
    }

    public function intermediate(): static
    {
        return $this->state(fn (array $attributes) => [
            'level_id' => Level::where('name', 'Средний')->first()->id
                ?? Level::factory()->create(['name' => 'Средний'])->id,
        ]);
    }

    public function advanced(): static
    {
        return $this->state(fn (array $attributes) => [
            'level_id' => Level::where('name', 'Продвинутый')->first()->id
                ?? Level::factory()->create(['name' => 'Продвинутый'])->id,
        ]);
    }

    public function weightLoss(): static
    {
        return $this->state(fn (array $attributes) => [
            'goal_id' => Goal::where('name', 'Похудение')->first()->id
                ?? Goal::factory()->create(['name' => 'Похудение'])->id,
        ]);
    }

    public function strength(): static
    {
        return $this->state(fn (array $attributes) => [
            'goal_id' => Goal::where('name', 'Сила')->first()->id
                ?? Goal::factory()->create(['name' => 'Сила'])->id,
        ]);
    }

    public function massGain(): static
    {
        return $this->state(fn (array $attributes) => [
            'goal_id' => Goal::where('name', 'Масса')->first()->id
                ?? Goal::factory()->create(['name' => 'Масса'])->id,
        ]);
    }
}
