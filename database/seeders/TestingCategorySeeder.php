<?php

namespace Database\Seeders;

use App\Models\TestingCategory;
use Illuminate\Database\Seeder;

class TestingCategorySeeder extends Seeder
{
    public function run(): void
    {
        TestingCategory::factory(15)->create();
    }
}
