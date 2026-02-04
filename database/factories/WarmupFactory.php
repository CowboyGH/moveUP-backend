<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warmup>
 */
class WarmupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $warmupTypes = [
            'Суставная гимнастика',
            'Динамическая растяжка',
            'Кардио разминка (легкий бег)',
            'Мобилизация суставов',
            'Активация мышц кора',
            'Дыхательные упражнения',
            'Подготовка ЦНС',
            'Разминка с эспандером'
        ];
        return [
            'name' => fake()->randomElement($warmupTypes),
            'description' => fake()->paragraph(2),
            'image' => 'warmups/' . fake()->image(null, 600, 400, 'sports', true),
        ];
    }
}
