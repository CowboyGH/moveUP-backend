<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $categories = [
            'Силовая тренировка',
            'Кардио тренировка',
            'Функциональный тренинг',
            'Йога и растяжка',
            'HIIT',
            'Кроссфит',
            'Пилатес',
            'Танцевальная аэробика',
            'Бокс и единоборства',
            'Реабилитация'
        ];

        return [
            'name' => fake()->unique()->randomElement($categories),
        ];
    }
}
