<?php

namespace Database\Seeders;

use App\Models\TestAttempt;
use App\Models\User;
use App\Models\Testing;
use Illuminate\Database\Seeder;

class TestAttemptSeeder extends Seeder
{
    public function run(): void
    {
        // Создаём 10 попыток для разных пользователей и тестов
        // Но напрямую мы не можем привязать пользователя к попытке, поэтому создаём попытки,
        // а пользователь будет определён позже через test_results.
        // Для простоты создадим попытки для существующих тестов.
        $testings = Testing::all();

        if ($testings->isEmpty()) {
            $this->call(TestingSeeder::class);
            $testings = Testing::all();
        }

        foreach (range(1, 10) as $i) {
            TestAttempt::factory()->create([
                'testing_id' => $testings->random()->id,
            ]);
        }
    }
}
