<?php

namespace App\Services\WorkoutGeneration\Assigner;

use App\Models\User;
use App\Models\UserWorkout;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkoutAssigner
{
    public function assign(User $user, Collection $workouts): void
    {
        DB::transaction(function () use ($user, $workouts) {
            $user->userWorkouts()
                ->whereIn('status', [UserWorkout::STATUS_ASSIGNED])
                ->delete();

            foreach ($workouts as $workout) {
                UserWorkout::create([
                    'user_id' => $user->id,
                    'workout_id' => $workout->id,
                    'status' => UserWorkout::STATUS_ASSIGNED,
                    'started_at' => null,
                    'completed_at' => null,
                ]);
            }

            Log::info("Назначено {$workouts->count()} тренировок пользователю {$user->id} (статус: assigned)");
        });
    }
}
