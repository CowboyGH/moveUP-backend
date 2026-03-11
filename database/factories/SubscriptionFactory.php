<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        $durationOptions = [30, 90, 180, 365];
        return [
            'name' => fake()->randomElement(['1 месяц', '3 месяца', '6 месяцев', '12 месяцев']),
            'description' => fake()->paragraph(),
            'price' => $this->getPriceByName($this->faker->randomElement(['1 месяц', '3 месяца', '6 месяцев', '12 месяцев'])),
            'duration_days' => fake()->randomElement($durationOptions),
            'is_active' => fake()->boolean(85),
        ];
    }

    /**
     * Получить цену по названию подписки
     */
    protected function getPriceByName(string $name): float
    {
        return match($name) {
            '1 месяц' => 500,
            '3 месяца' => 1400,
            '6 месяцев' => 2700,
            '12 месяцев' => 5000,
            default => 500,
        };
    }

    public function basicOneMonth(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '1 месяц',
            'price' => 500,
            'duration_days' => 30,
            'is_active' => true,
        ]);
    }

    public function proThreeMonths(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '3 месяца',
            'price' => 1400,
            'duration_days' => 90,
            'is_active' => true,
        ]);
    }

    public function premiumSixMonths(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '6 месяцев',
            'price' => 2700,
            'duration_days' => 180,
            'is_active' => true,
        ]);
    }

    public function ultimateYearly(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '12 месяцев',
            'price' => 5000,
            'duration_days' => 365,
            'is_active' => true,
        ]);
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
}
