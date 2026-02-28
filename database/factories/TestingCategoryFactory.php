<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Testing;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestingCategoryFactory extends Factory
{
    public function definition(): array
    {
        static $testingCategoryCounts = [];

        $testingId = Testing::inRandomOrder()->first()->id
            ?? Testing::factory()->create()->id;

        $currentCount = $testingCategoryCounts[$testingId] ?? 0;

        if ($currentCount >= 4) {
            $testingId = Testing::whereDoesntHave('testingCategories', function($q) {
                $q->groupBy('testing_id')
                    ->havingRaw('COUNT(*) < 4');
            })->inRandomOrder()->first()?->id
                ?? Testing::factory()->create()->id;
        }

        $testingCategoryCounts[$testingId] = ($testingCategoryCounts[$testingId] ?? 0) + 1;

        return [
            'testing_id' => $testingId,
            'category_id' => Category::inRandomOrder()->first()->id
                ?? Category::factory()->create()->id,
        ];
    }
}
