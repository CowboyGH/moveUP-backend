<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(SubscriptionSeeder::class);
        $this->call(PhaseSeeder::class);

        $this->call(EquipmentSeeder::class);
        $this->call(LevelSeeder::class);
        $this->call(GoalSeeder::class);
        $this->call(CategorySeeder::class);

        $this->call(UserSeeder::class);
        $this->call(UserSubscriptionSeeder::class);
        $this->call(UserParameterSeeder::class);
        $this->call(UserProgressSeeder::class);

        $this->call(ExerciseSeeder::class);
        $this->call(WarmupSeeder::class);
        $this->call(WorkoutSeeder::class);
        $this->call(WorkoutExerciseSeeder::class);
        $this->call(WorkoutWarmupSeeder::class);

        $this->call(TestingSeeder::class);
        $this->call(TestingExerciseSeeder::class);
        $this->call(TestingTestExerciseSeeder::class);
        $this->call(TestingCategorySeeder::class);
        $this->call(TestResultSeeder::class);

        $this->call(UserWorkoutSeeder::class);
        $this->call(ExercisePerformanceSeeder::class);
        $this->call(UserWarmupPerformanceSeeder::class);

        $this->call(SavedCardSeeder::class);
        $this->call(PaymentSeeder::class);
    }
}
