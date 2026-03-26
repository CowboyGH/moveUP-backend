<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="TestingExercise",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="description", type="string", example="Отжимания от пола - максимальное количество за 1 минуту"),
 *     @OA\Property(property="image", type="string", example="/uploads/exercises/pushups.jpg"),
 *     @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z")
 * )
 */
class TestingSchemas {}

/**
 * @OA\Schema(
 *     schema="TestingExerciseWithPivot",
 *     type="object",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/TestingExercise"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="pivot",
 *                 type="object",
 *                 @OA\Property(property="testing_id", type="integer", example=72),
 *                 @OA\Property(property="testing_exercise_id", type="integer", example=1),
 *                 @OA\Property(property="order_number", type="integer", example=0),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z")
 *             )
 *         )
 *     }
 * )
 */
class TestingExerciseWithPivotSchema {}

/**
 * @OA\Schema(
 *     schema="Testing",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=72),
 *     @OA\Property(property="title", type="string", example="Базовая диагностика"),
 *     @OA\Property(property="description", type="string", example="Тест для определения базового уровня физической подготовки"),
 *     @OA\Property(property="duration_minutes", type="string", example="15-20 минут"),
 *     @OA\Property(property="image", type="string", example="/uploads/tests/basic-diagnostic.jpg"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *     @OA\Property(property="test_results_count", type="integer", example=0),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Category")
 *     ),
 *     @OA\Property(
 *         property="test_exercises",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/TestingExerciseWithPivot")
 *     )
 * )
 */
class TestingSchema {}

/**
 * @OA\Schema(
 *     schema="TestingWithResults",
 *     type="object",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Testing"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="test_results",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/TestResult")
 *             )
 *         )
 *     }
 * )
 */
class TestingWithResultsSchema {}

/**
 * @OA\Schema(
 *     schema="TestResult",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=5),
 *     @OA\Property(property="testing_id", type="integer", example=72),
 *     @OA\Property(property="exercise_id", type="integer", example=1),
 *     @OA\Property(property="result_value", type="integer", example=25),
 *     @OA\Property(property="pulse", type="integer", example=120, nullable=true),
 *     @OA\Property(property="test_date", type="string", format="date", example="2026-02-20"),
 *     @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z")
 * )
 */
class TestResultSchema {}
