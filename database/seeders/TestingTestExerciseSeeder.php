<?php

namespace Database\Seeders;

use App\Models\Testing;
use App\Models\TestingExercise;
use App\Models\TestingTestExercise;
use Illuminate\Database\Seeder;
use RuntimeException;

class TestingTestExerciseSeeder extends Seeder
{
    private array $testToExercises = [
        'Тест Купера (12-минутный бег)' => ['12-минутный бег'],
        'Гарвардский степ-тест' => ['Гарвардский степ-тест'],
        'Тест Руфье' => ['Тест Руфье (приседания)'],
        'Определение максимальной силы (1ПМ)' => [
            'Жим лежа (1ПМ)',
            'Приседания со штангой (1ПМ)',
            'Становая тяга (1ПМ)',
        ],
        'Гибкость: Тест "Сядь и достань"' => ['Наклон вперед сидя'],
        'Тест на выносливость мышц кора' => [
            'Планка',
            'Скручивания',
            'Гиперэкстензия',
        ],
        'Взрывная сила: Прыжок в длину с места' => ['Прыжок в длину с места'],
        'Скоростно-силовая выносливость' => ['Берпи'],
        'Координация и ловкость: Челночный бег' => ['Челночный бег 3x10 м'],
        'Баланс и стабильность' => ['Стойка на одной ноге'],
    ];

    public function run(): void
    {
        $testings = Testing::all();
        $exerciseByTitle = TestingExercise::all()->keyBy('title');

        if ($testings->isEmpty()) {
            $this->command->error('Нет тестов! Сначала запустите TestingSeeder.');
            return;
        }

        if ($exerciseByTitle->isEmpty()) {
            $this->command->error('Нет тестовых упражнений! Сначала запустите TestingExerciseSeeder.');
            return;
        }

        $totalCreated = 0;

        $this->command->info("Привязываем тестовые упражнения к тестам...");

        foreach ($testings as $testing) {
            if (!isset($this->testToExercises[$testing->title])) {
                throw new RuntimeException(
                    "TestingTestExerciseSeeder: нет маппинга для теста '{$testing->title}'. "
                    . "Добавь его в \$testToExercises или удали тест из TestingSeeder."
                );
            }

            TestingTestExercise::where('testing_id', $testing->id)->delete();

            $orderNumber = 1;
            foreach ($this->testToExercises[$testing->title] as $exerciseTitle) {
                $exercise = $exerciseByTitle->get($exerciseTitle);
                if (!$exercise) {
                    throw new RuntimeException(
                        "TestingTestExerciseSeeder: упражнение '{$exerciseTitle}' не найдено. "
                        . "Проверь TestingExerciseSeeder."
                    );
                }

                TestingTestExercise::create([
                    'testing_id' => $testing->id,
                    'testing_exercise_id' => $exercise->id,
                    'order_number' => $orderNumber++,
                ]);
                $totalCreated++;
            }

            $count = count($this->testToExercises[$testing->title]);
            $this->command->info("✓ Тест '{$testing->title}' получил {$count} упражнений");
        }

        $this->command->info("Всего создано {$totalCreated} связей тест-тестовое упражнение");
    }
}
