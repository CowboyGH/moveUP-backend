<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Testing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TestingCategory>
 */
class TestingCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'testing_id' => Testing::inRandomOrder()->first()->id
                ?? Testing::factory()->create()->id,
            'category_id' => Category::inRandomOrder()->first()->id
                ?? Category::factory()->create()->id,
        ];
    }
}
