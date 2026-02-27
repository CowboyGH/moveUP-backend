<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Phase;
use App\Models\UserProgress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        UserProgress::factory()->count(10)->newUser()->create();

        UserProgress::factory()->count(20)->active()->create();

        UserProgress::factory()->count(10)->advanced()->create();

        UserProgress::factory()->count(5)->readyToAdvance()->create();

        UserProgress::factory()->count(3)->perfectStreak()->create();

        User::whereDoesntHave('userProgress')
            ->get()
            ->each(function ($user) {
                $factory = UserProgress::factory()->forUser($user);

                $progressFactory = match (fake()->numberBetween(1, 4)) {
                    1 => $factory->newUser(),
                    2 => $factory->active(),
                    3 => $factory->advanced(),
                    4 => $factory->readyToAdvance(),
                };

                $progressFactory->create();
            });
    }
}
