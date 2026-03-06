<?php

namespace Database\Seeders;

use App\Models\TestAttempt;
use App\Models\TestResult;
use App\Models\TestingTestExercise;
use Illuminate\Database\Seeder;

class TestResultSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем все попытки (они уже созданы в TestAttemptSeeder)
        $attempts = TestAttempt::all();

        if ($attempts->isEmpty()) {
            $this->call(TestAttemptSeeder::class);
            $attempts = TestAttempt::all();
        }

        foreach ($attempts as $attempt) {
            // Получаем все упражнения, относящиеся к тесту этой попытки
            $testExercises = TestingTestExercise::where('testing_id', $attempt->testing_id)
                ->with('testingExercise')
                ->get();

            if ($testExercises->isEmpty()) {
                continue; // пропускаем попытки без упражнений
            }

            // Для каждого упражнения создаём результат
            foreach ($testExercises as $tte) {
                // Определяем пользователя: можно взять случайного, либо создать нового
                // Здесь для простоты используем фабрику, но пользователь должен быть один на всю попытку.
                // Так как у нас нет привязки пользователя к попытке, создадим результат для случайного пользователя.
                // Более правильно: в TestAttempt добавить user_id, но мы отказались от этого.
                // Поэтому при создании результатов для одной попытки пользователь будет разный – это не страшно для тестов.

                TestResult::factory()->create([
                    'test_attempt_id' => $attempt->id,
                    'testing_id' => $attempt->testing_id,
                    'testing_exercise_id' => $tte->testing_exercise_id,
                ]);
            }
        }
    }
}
