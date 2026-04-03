<?php

namespace Database\Seeders;

use App\Models\Workout;
use App\Models\Exercise;
use App\Models\WorkoutExercise;
use Illuminate\Database\Seeder;

class WorkoutExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $workouts = Workout::all();
        $exercises = Exercise::all();

        if ($workouts->isEmpty()) {
            $this->command->error('Нет тренировок! Сначала запустите WorkoutSeeder.');
            return;
        }

        if ($exercises->isEmpty()) {
            $this->command->error('Нет упражнений! Сначала запустите ExerciseSeeder.');
            return;
        }

        $totalCreated = 0;
        $workoutCount = $workouts->count();

        $this->command->info("Начинаем привязку упражнений к {$workoutCount} тренировкам...");

        foreach ($workouts as $workout) {
            $exerciseCount = $this->getExerciseCountForWorkout($workout->type);

            WorkoutExercise::where('workout_id', $workout->id)->delete();

            $orderNumber = 1;
            $workoutCreated = 0;

            for ($i = 0; $i < $exerciseCount; $i++) {
                $exercise = $exercises->random();

                WorkoutExercise::create([
                    'workout_id' => $workout->id,
                    'exercise_id' => $exercise->id,
                    'sets' => $this->getSetsByWorkoutType($workout->type),
                    'reps' => $this->getRepsByWorkoutType($workout->type),
                    'order_number' => $orderNumber++,
                ]);

                $workoutCreated++;
                $totalCreated++;
            }

            $this->command->info("Для тренировки ID {$workout->id} '{$workout->title}' добавлено {$workoutCreated} упражнений");
        }

        $this->command->info("ГОТОВО: Всего создано {$totalCreated} связей тренировка-упражнение для {$workoutCount} тренировок");
    }

    private function getExerciseCountForWorkout(string $type): int
    {
        return match($type) {
            'strength' => 8,
            'hypertrophy' => 7,
            'power' => 6,
            'hiit' => 6,
            'circuit' => 6,
            'functional' => 7,
            'cardio' => 5,
            'general' => 6,
            default => 6,
        };
    }

    private function getSetsByWorkoutType(string $type): int
    {
        return match($type) {
            'strength' => 4,
            'hypertrophy' => 4,
            'power' => 5,
            'hiit' => 3,
            'circuit' => 3,
            'functional' => 3,
            'cardio' => 3,
            'general' => 3,
            default => 3,
        };
    }

    private function getRepsByWorkoutType(string $type): int
    {
        return match($type) {
            'strength' => 8,
            'hypertrophy' => 12,
            'power' => 5,
            'hiit' => 20,
            'circuit' => 15,
            'functional' => 12,
            'cardio' => 25,
            'general' => 10,
            default => 10,
        };
    }
}
