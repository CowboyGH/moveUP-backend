<?php

namespace Database\Seeders;

use App\Models\TestingCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestingCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TestingCategory::factory(20)->create();
    }
}
