<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('local')) {
            $this->call(RoleSeeder::class);
            $this->call(SubscriptionSeeder::class);
            $this->call(PhaseSeeder::class);
            $this->call(EquipmentSeeder::class);
            $this->call(LevelSeeder::class);
            $this->call(GoalSeeder::class);

            $this->call(ExerciseSeeder::class);
            $this->call(WarmupSeeder::class);
            $this->call(WorkoutSeeder::class);

            $this->call(CategorySeeder::class);
            $this->call(TestingSeeder::class);
            $this->call(TestingExerciseSeeder::class);
            $this->call(TestingTestExerciseSeeder::class);
            $this->call(TestingCategorySeeder::class);
        }
    }
}
