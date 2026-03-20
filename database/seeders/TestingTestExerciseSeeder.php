<?php

namespace Database\Seeders;

use App\Models\Testing;
use App\Models\TestingExercise;
use App\Models\TestingTestExercise;
use Illuminate\Database\Seeder;

class TestingTestExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $testings = Testing::all();
        $testingExercises = TestingExercise::all();

        if ($testings->isEmpty()) {
            $this->command->error('Нет тестов! Сначала запустите TestingSeeder.');
            return;
        }

        if ($testingExercises->isEmpty()) {
            $this->command->error('Нет тестовых упражнений! Сначала запустите TestingExerciseSeeder.');
            return;
        }

        $totalCreated = 0;

        $this->command->info("Привязываем тестовые упражнения к тестам...");

        foreach ($testings as $testing) {
            $matchingExercises = $this->getMatchingExercises($testing->title, $testingExercises);

            if ($matchingExercises->isEmpty()) {
                $this->command->warn("Для теста '{$testing->title}' не найдено подходящих упражнений");
                continue;
            }

            TestingTestExercise::where('testing_id', $testing->id)->delete();

            $orderNumber = 1;

            foreach ($matchingExercises as $exercise) {
                TestingTestExercise::create([
                    'testing_id' => $testing->id,
                    'testing_exercise_id' => $exercise->id,
                    'order_number' => $orderNumber++,
                ]);
                $totalCreated++;
            }

            $this->command->info("✓ Тест '{$testing->title}' получил {$matchingExercises->count()} упражнений");
        }

        $this->command->info("Всего создано {$totalCreated} связей тест-тестовое упражнение");
    }

    private function getMatchingExercises(string $testTitle, $allExercises)
    {
        $collection = collect($allExercises);

        if (str_contains($testTitle, 'Купера') || str_contains($testTitle, 'бег')) {
            return $collection->filter(function ($exercise) {
                return str_contains($exercise->description, 'бег') ||
                    str_contains($exercise->description, 'дистанцию');
            });
        }

        if (str_contains($testTitle, 'Гарвардский')) {
            return $collection->filter(function ($exercise) {
                return str_contains($exercise->description, 'степ') ||
                    str_contains($exercise->description, 'восхождение');
            });
        }

        if (str_contains($testTitle, 'Руфье')) {
            return $collection->filter(function ($exercise) {
                return str_contains($exercise->description, 'приседания');
            });
        }

        if (str_contains($testTitle, '1ПМ') || str_contains($testTitle, 'максимальной силы')) {
            return $collection->filter(function ($exercise) {
                return str_contains($exercise->description, 'жим') ||
                    str_contains($exercise->description, 'приседания') ||
                    str_contains($exercise->description, 'тяга');
            });
        }

        if (str_contains($testTitle, 'Гибкость')) {
            return $collection->filter(function ($exercise) {
                return str_contains($exercise->description, 'наклон') ||
                    str_contains($exercise->description, 'гибкость');
            });
        }

        if (str_contains($testTitle, 'кора') || str_contains($testTitle, 'выносливость мышц кора')) {
            return $collection->filter(function ($exercise) {
                return str_contains($exercise->description, 'планка') ||
                    str_contains($exercise->description, 'скручивания') ||
                    str_contains($exercise->description, 'гиперэкстензия');
            });
        }

        if (str_contains($testTitle, 'Взрывная') || str_contains($testTitle, 'прыжок')) {
            return $collection->filter(function ($exercise) {
                return str_contains($exercise->description, 'прыжок');
            });
        }

        if (str_contains($testTitle, 'берпи') || str_contains($testTitle, 'Скоростно-силовая')) {
            return $collection->filter(function ($exercise) {
                return str_contains($exercise->description, 'берпи');
            });
        }

        if (str_contains($testTitle, 'Челночный') || str_contains($testTitle, 'ловкость')) {
            return $collection->filter(function ($exercise) {
                return str_contains($exercise->description, 'челночный') ||
                    str_contains($exercise->description, 'бег');
            });
        }

        if (str_contains($testTitle, 'Баланс')) {
            return $collection->filter(function ($exercise) {
                return str_contains($exercise->description, 'стойка') ||
                    str_contains($exercise->description, 'баланс');
            });
        }

        return $collection->random(min(4, $collection->count()));
    }
}
