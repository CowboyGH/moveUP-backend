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
        $attempts = TestAttempt::all();

        if ($attempts->isEmpty()) {
            $this->command->error('Нет попыток! Сначала запустите TestAttemptSeeder.');
            return;
        }

        $totalCreated = 0;

        foreach ($attempts as $attempt) {
            $testExercises = TestingTestExercise::where('testing_id', $attempt->testing_id)
                ->with('testingExercise')
                ->get();

            if ($testExercises->isEmpty()) {
                $this->command->warn("Для теста ID {$attempt->testing_id} нет упражнений");
                continue;
            }

            foreach ($testExercises as $testExercise) {
                TestResult::factory()->create([
                    'test_attempt_id' => $attempt->id,
                    'testing_id' => $attempt->testing_id,
                    'testing_exercise_id' => $testExercise->testing_exercise_id,
                ]);
                $totalCreated++;
            }
        }

        $this->command->info("Всего создано {$totalCreated} результатов тестов");
    }
}
