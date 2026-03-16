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
        UserWorkout::factory(50)->create();

        $users = User::take(5)->get();
        $workouts = Workout::take(10)->get();

        foreach ($users as $user) {
            UserWorkout::factory()
                ->count(3)
                ->assigned()
                ->forUser($user->id)
                ->create();

            UserWorkout::factory()
                ->count(2)
                ->started()
                ->forUser($user->id)
                ->create();

            UserWorkout::factory()
                ->count(5)
                ->completed()
                ->forUser($user->id)
                ->create();
        }
        UserWorkout::factory()
            ->count(30)
            ->create();
    }
}
