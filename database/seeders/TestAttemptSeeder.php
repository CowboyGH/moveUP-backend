<?php

namespace Database\Seeders;

use App\Models\TestAttempt;
use App\Models\Testing;
use Illuminate\Database\Seeder;

class TestAttemptSeeder extends Seeder
{
    public function run(): void
    {
        $testings = Testing::all();

        if ($testings->isEmpty()) {
            $this->command->error('Нет тестов! Сначала запустите TestingSeeder.');
            return;
        }

        $totalCreated = 0;

        // Создаем попытки для каждого теста
        foreach ($testings as $testing) {
            // Для каждого теста создаем от 3 до 10 попыток
            $attemptsCount = rand(3, 10);

            for ($i = 0; $i < $attemptsCount; $i++) {
                TestAttempt::factory()->create([
                    'testing_id' => $testing->id,
                ]);
                $totalCreated++;
            }

            $this->command->info("✓ Для теста '{$testing->title}' создано {$attemptsCount} попыток");
        }

        $this->command->info("Всего создано {$totalCreated} попыток прохождения тестов");
    }
}
