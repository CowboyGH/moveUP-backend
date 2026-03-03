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
        $phase = Phase::inRandomOrder()->first() ?? Phase::factory()->create();

        $workoutTypes = [
            [
                'title' => 'Базовая силовая тренировка',
                'type' => 'strength',
                'description' => 'Фундаментальные упражнения для развития силы'
            ],
            [
                'title' => 'Тренировка ног и ягодиц',
                'type' => 'strength',
                'description' => 'Приседания, выпады, ягодичный мостик для мощных ног'
            ],
            [
                'title' => 'Силовая на верх тела',
                'type' => 'strength',
                'description' => 'Жимы, тяги и подтягивания для верхней части тела'
            ],
            [
                'title' => 'Тренировка на силу и мощь',
                'type' => 'power',
                'description' => 'Взрывные движения и работа с максимальными весами'
            ],

            [
                'title' => 'Объемная тренировка на массу',
                'type' => 'hypertrophy',
                'description' => 'Многоповторная работа для роста мышечной массы'
            ],
            [
                'title' => 'Тренировка рук и плеч',
                'type' => 'hypertrophy',
                'description' => 'Изолированная работа на бицепс, трицепс и дельты'
            ],
            [
                'title' => 'Тренировка спины и груди',
                'type' => 'hypertrophy',
                'description' => 'Упражнения для развития широкой спины и грудных мышц'
            ],

            [
                'title' => 'Кардио сессия для сжигания жира',
                'type' => 'cardio',
                'description' => 'Бег, велосипед, эллипс для активного жиросжигания'
            ],
            [
                'title' => 'Интервальный бег',
                'type' => 'cardio',
                'description' => 'Чередование спринта и бега трусцой'
            ],

            [
                'title' => 'HIIT тренировка на все тело',
                'type' => 'hiit',
                'description' => 'Интенсивные интервалы с коротким отдыхом'
            ],
            [
                'title' => 'Табата тренировка',
                'type' => 'hiit',
                'description' => '20 секунд работы, 10 отдыха - 8 раундов'
            ],

            [
                'title' => 'Круговая тренировка на выносливость',
                'type' => 'circuit',
                'description' => '5-6 упражнений, выполняемых по кругу без отдыха'
            ],
            [
                'title' => 'Функциональный круг',
                'type' => 'circuit',
                'description' => 'Сочетание силовых и кардио упражнений'
            ],

            [
                'title' => 'Функциональный тренинг',
                'type' => 'functional',
                'description' => 'Движения, имитирующие повседневную активность'
            ],
            [
                'title' => 'Тренировка на равновесие и координацию',
                'type' => 'functional',
                'description' => 'Упражнения с нестабильной поверхностью'
            ],

            [
                'title' => 'Тренировка для начинающих',
                'type' => 'general',
                'description' => 'Простые упражнения для знакомства с тренировками'
            ],
            [
                'title' => 'Общая физическая подготовка',
                'type' => 'general',
                'description' => 'Сбалансированная тренировка на все группы мышц'
            ],
        ];

        $selectedType = fake()->randomElement($workoutTypes);

        return [
            'phase_id' => $phase->id,
            'title' => $selectedType['title'],
            'type' => $selectedType['type'],
            'description' => $selectedType['description'] . ' ' . fake()->paragraph(2),
            'duration_minutes' => fake()->numberBetween(20, 60),
            'is_active' => true,
        ];
    }

    public function forPhase(Phase $phase): static
    {
        return $this->state(fn (array $attributes) => [
            'phase_id' => $phase->id,
        ]);
    }

    public function ofType(string $type): static
    {
        return $this->state(function (array $attributes) use ($type) {
            $titles = [
                'strength' => 'Силовая тренировка',
                'power' => 'Тренировка на мощность',
                'hypertrophy' => 'Тренировка на массу',
                'cardio' => 'Кардио тренировка',
                'hiit' => 'HIIT тренировка',
                'circuit' => 'Круговая тренировка',
                'functional' => 'Функциональный тренинг',
                'general' => 'ОФП тренировка',
            ];

            return [
                'type' => $type,
                'title' => $titles[$type] ?? 'Тренировка',
            ];
        });
    }

    public function short(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_minutes' => fake()->numberBetween(15, 30),
            'title' => 'Экспресс-' . ($attributes['title'] ?? 'тренировка'),
        ]);
    }

    public function medium(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_minutes' => fake()->numberBetween(30, 45),
        ]);
    }

    public function long(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_minutes' => fake()->numberBetween(45, 60),
        ]);
    }
}
