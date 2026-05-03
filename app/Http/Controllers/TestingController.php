<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Testing;
use Illuminate\Http\JsonResponse;

class TestingController extends Controller
{
    public function index(): JsonResponse
    {
        $testings = Testing::where('is_active', 1)
            ->with(['categories', 'testExercises'])
            ->get()
            ->map(function ($testing) {
                return [
                    'id' => $testing->id,
                    'title' => $testing->title,
                    'description' => $testing->description,
                    'duration_minutes' => $testing->duration_minutes,
                    'image' => $testing->image,
                    'categories' => $testing->categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                        ];
                    }),
                    'exercises_count' => $testing->testExercises->count(),
                ];
            });

        return ApiResponse::data($testings);
    }

}
