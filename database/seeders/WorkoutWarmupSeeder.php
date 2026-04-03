<?php

namespace Database\Seeders;

use App\Models\Workout;
use App\Models\Warmup;
use App\Models\WorkoutWarmup;
use Illuminate\Database\Seeder;

class WorkoutWarmupSeeder extends Seeder
{
    public function run(): void
    {
        $workouts = Workout::all();
        $warmups = Warmup::all();

        if ($workouts->isEmpty()) {
            $this->command->error('Нет тренировок! Сначала запустите WorkoutSeeder.');
            return;
        }

        if ($warmups->isEmpty()) {
            $this->command->error('Нет разминок! Сначала запустите WarmupSeeder.');
            return;
        }

        $totalCreated = 0;
        $workoutCount = $workouts->count();

        $this->command->info("Начинаем привязку разминок к {$workoutCount} тренировкам...");

        foreach ($workouts as $workout) {
            $warmupCount = $this->getWarmupCountForWorkout($workout->type);

            WorkoutWarmup::where('workout_id', $workout->id)->delete();

            $orderNumber = 1;
            $workoutCreated = 0;

            for ($i = 0; $i < $warmupCount; $i++) {
                $warmup = $warmups->random();

                WorkoutWarmup::create([
                    'workout_id' => $workout->id,
                    'warmup_id' => $warmup->id,
                    'order_number' => $orderNumber++,
                ]);

                $workoutCreated++;
                $totalCreated++;
            }

            $this->command->info("Для тренировки ID {$workout->id} '{$workout->title}' добавлено {$workoutCreated} разминок");
        }

        $this->command->info("ГОТОВО: Всего создано {$totalCreated} связей тренировка-разминка для {$workoutCount} тренировок");
    }

    private function getWarmupCountForWorkout(string $type): int
    {
        return match($type) {
            'strength' => 2,
            'hypertrophy' => 2,
            'hiit' => 2,
            'circuit' => 2,
            'functional' => 2,
            'cardio' => 1,
            'general' => 2,
            default => 2,
        };
    }
}
