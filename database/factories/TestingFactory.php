<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TestingFactory extends Factory
{
    private array $realTests = [
        [
            'title' => 'Тест Купера (12-минутный бег)',
            'description' => 'Измерение аэробной выносливости. Необходимо пробежать максимальную дистанцию за 12 минут. Результат оценивается по таблице возрастных норм.',
            'duration_minutes' => 12,
            'image' => 'tests/cooper-test.jpg'
        ],
        [
            'title' => 'Гарвардский степ-тест',
            'description' => 'Оценка восстановления сердечно-сосудистой системы после нагрузки. Выполняется степ-тест с измерением пульса на 1-й, 2-й и 3-й минутах восстановления.',
            'duration_minutes' => 15,
            'image' => 'tests/harvard-step.jpg'
        ],
        [
            'title' => 'Тест Руфье',
            'description' => 'Функциональная проба для оценки работоспособности сердца. Измеряется пульс в покое, после 30 приседаний и через минуту восстановления.',
            'duration_minutes' => 5,
            'image' => 'tests/ruffier.jpg'
        ],
        [
            'title' => 'Определение максимальной силы (1ПМ)',
            'description' => 'Тестирование максимального веса в базовых упражнениях: жим лежа, приседания, становая тяга. Определяется разовый максимум с подходами.',
            'duration_minutes' => 40,
            'image' => 'tests/1rm.jpg'
        ],
        [
            'title' => 'Гибкость: Тест "Сядь и достань"',
            'description' => 'Оценка гибкости поясницы и задней поверхности бедра. Измеряется расстояние от кончиков пальцев до стоп в положении сидя.',
            'duration_minutes' => 5,
            'image' => 'tests/flexibility.jpg'
        ],
    ];

    public function definition(): array
    {
        $test = $this->faker->randomElement($this->realTests);

        return [
            'title' => $test['title'],
            'description' => $test['description'],
            'duration_minutes' => $test['duration_minutes'],
            'image' => $test['image'],
            'is_active' => true,
        ];
    }
}
