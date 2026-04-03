<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workout;
use App\Models\UserWorkout;
use Illuminate\Database\Seeder;

class UserWorkoutSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereHas('role', fn($q) => $q->where('name', 'user'))->get();
        $workouts = Workout::all();

        if ($users->isEmpty()) {
            $this->command->error('Нет пользователей! Сначала запустите UserSeeder.');
            return;
        }

        if ($workouts->isEmpty()) {
            $this->command->error('Нет тренировок! Сначала запустите WorkoutSeeder.');
            return;
        }

        $totalCreated = 0;
        $workoutIds = $workouts->pluck('id')->toArray();

        foreach ($users as $user) {

            for ($i = 0; $i < 3; $i++) {
                UserWorkout::factory()
                    ->assigned()
                    ->forUser($user->id)
                    ->forWorkout($this->getRandomWorkoutId($workoutIds))
                    ->create();
                $totalCreated++;
            }

            for ($i = 0; $i < 2; $i++) {
                UserWorkout::factory()
                    ->started()
                    ->forUser($user->id)
                    ->forWorkout($this->getRandomWorkoutId($workoutIds))
                    ->create();
                $totalCreated++;
            }

            for ($i = 0; $i < 5; $i++) {
                UserWorkout::factory()
                    ->completed()
                    ->forUser($user->id)
                    ->forWorkout($this->getRandomWorkoutId($workoutIds))
                    ->create();
                $totalCreated++;
            }
        }

        $this->command->info("Создано {$totalCreated} записей user_workouts для " . $users->count() . " пользователей");
    }

    private function getRandomWorkoutId(array $workoutIds): int
    {
        return $workoutIds[array_rand($workoutIds)];
    }
}
