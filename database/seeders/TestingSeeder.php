<?php

namespace Database\Seeders;

use App\Models\Testing;
use Illuminate\Database\Seeder;

class TestingSeeder extends Seeder
{
    private array $realTests = [
        [
            'title' => 'Тест Купера (12-минутный бег)',
            'description' => 'Измерение аэробной выносливости. Необходимо пробежать максимальную дистанцию за 12 минут. Результат оценивается по таблице возрастных норм.',
            'duration_minutes' => 12,
            'image' => 'tests/cooper-test.jpg',
            'is_active' => true
        ],
        [
            'title' => 'Гарвардский степ-тест',
            'description' => 'Оценка восстановления сердечно-сосудистой системы после нагрузки. Выполняется степ-тест с измерением пульса на 1-й, 2-й и 3-й минутах восстановления.',
            'duration_minutes' => 15,
            'image' => 'tests/harvard-step.jpg',
            'is_active' => true
        ],
        [
            'title' => 'Тест Руфье',
            'description' => 'Функциональная проба для оценки работоспособности сердца. Измеряется пульс в покое, после 30 приседаний и через минуту восстановления.',
            'duration_minutes' => 5,
            'image' => 'tests/ruffier.jpg',
            'is_active' => true
        ],
        [
            'title' => 'Определение максимальной силы (1ПМ)',
            'description' => 'Тестирование максимального веса в базовых упражнениях: жим лежа, приседания, становая тяга. Определяется разовый максимум с подходами.',
            'duration_minutes' => 40,
            'image' => 'tests/1rm.jpg',
            'is_active' => true
        ],
        [
            'title' => 'Гибкость: Тест "Сядь и достань"',
            'description' => 'Оценка гибкости поясницы и задней поверхности бедра. Измеряется расстояние от кончиков пальцев до стоп в положении сидя.',
            'duration_minutes' => 5,
            'image' => 'tests/flexibility.jpg',
            'is_active' => true
        ],
        [
            'title' => 'Тест на выносливость мышц кора',
            'description' => 'Тестирование силовой выносливости мышц пресса и спины. Включает удержание планки, скручивания и гиперэкстензию на время.',
            'duration_minutes' => 15,
            'image' => 'tests/core-endurance.jpg',
            'is_active' => true
        ],
        [
            'title' => 'Взрывная сила: Прыжок в длину с места',
            'description' => 'Оценка взрывной силы мышц ног. Выполняется три попытки, засчитывается лучший результат.',
            'duration_minutes' => 10,
            'image' => 'tests/vertical-jump.jpg',
            'is_active' => true
        ],
        [
            'title' => 'Скоростно-силовая выносливость',
            'description' => 'Тест на максимальное количество берпи за 2 минуты. Оценивает общую функциональную подготовку.',
            'duration_minutes' => 2,
            'image' => 'tests/burpee-test.jpg',
            'is_active' => true
        ],
        [
            'title' => 'Координация и ловкость: Челночный бег',
            'description' => 'Тест на скорость, ловкость и координацию. Три отрезка по 10 метров с касанием линии.',
            'duration_minutes' => 5,
            'image' => 'tests/shuttle-run.jpg',
            'is_active' => true
        ],
        [
            'title' => 'Баланс и стабильность',
            'description' => 'Оценка вестибулярного аппарата и стабильности тела. Стойка на одной ноге с закрытыми глазами.',
            'duration_minutes' => 3,
            'image' => 'tests/balance.jpg',
            'is_active' => true
        ],
    ];

    public function run(): void
    {
        foreach ($this->realTests as $testData) {
            Testing::updateOrCreate(
                ['title' => $testData['title']],
                $testData
            );
        }
        Testing::factory(5)->create();

        $this->command->info('Создано ' . Testing::count() . ' тестов');
    }
}
