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
            $testing = Testing::whereDoesntHave('categories', function($q) {
                $q->select(\DB::raw('count(*)'));
            }, '<', 4)
                ->inRandomOrder()
                ->first();

            $testingId = $testing?->id ?? Testing::factory()->create()->id;
        }

        $testingCategoryCounts[$testingId] = ($testingCategoryCounts[$testingId] ?? 0) + 1;

        return [
            'testing_id' => $testingId,
            'category_id' => Category::inRandomOrder()->first()->id
                ?? Category::factory()->create()->id,
        ];
    }
}
