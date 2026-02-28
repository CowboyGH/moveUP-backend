<?php

namespace Database\Seeders;

use App\Models\UserProgress;
use Illuminate\Database\Seeder;

class UserProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserProgress::factory(30)->create();
    }
}
