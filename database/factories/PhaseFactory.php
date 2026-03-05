<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PhaseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true) . ' фаза',
            'description' => fake()->paragraphs(3, true),
            'duration_days' => fake()->randomElement([7, 14, 21, 28, 30]),
            'order_number' => fake()->unique()->numberBetween(1, 10),
        ];
    }

    public function preparation(): static
    {
        return $this->state([
            'name' => 'Подготовительная фаза',
            'description' => 'Начальный этап для адаптации к тренировкам. В этой фазе вы освоите базовые движения и подготовите тело к более интенсивным нагрузкам.',
            'order_number' => 1,
            'duration_days' => 7,
        ]);
    }

    public function basic(): static
    {
        return $this->state([
            'name' => 'Базовая фаза',
            'description' => 'Формирование базовых навыков и силы. Увеличение рабочих весов и освоение правильной техники выполнения упражнений.',
            'order_number' => 2,
            'duration_days' => 14,
        ]);
    }

    public function intense(): static
    {
        return $this->state([
            'name' => 'Интенсивная фаза',
            'description' => 'Максимальные нагрузки и прогресс. Работа на пределе возможностей для достижения максимальных результатов.',
            'order_number' => 3,
            'duration_days' => 21,
        ]);
    }

    public function rest(): static
    {
        return $this->state([
            'name' => 'Фаза отдыха',
            'description' => 'Восстановление и легкие тренировки. Активное восстановление, растяжка и работа над техникой.',
            'order_number' => 4,
            'duration_days' => 7,
        ]);
    }

    public function advanced(): static
    {
        return $this->state([
            'name' => 'Продвинутая фаза',
            'description' => 'Сложные комплексы и специализированные тренировки для опытных спортсменов.',
            'order_number' => 5,
            'duration_days' => 30,
        ]);
    }

    /**
     * Создать фазу с определенным порядковым номером
     */
    public function withOrder(int $order): static
    {
        return $this->state([
            'order_number' => $order,
        ]);
    }
}
