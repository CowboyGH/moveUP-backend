<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Категория теста",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Бокс и единоборства"),
 *     @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z")
 * )
 */
class CategorySchema {}

/**
 * @OA\Schema(
 *     schema="CategoryWithPivot",
 *     type="object",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Category"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="pivot",
 *                 type="object",
 *                 @OA\Property(property="category_id", type="integer", example=1),
 *                 @OA\Property(property="testing_id", type="integer", example=72),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z")
 *             )
 *         )
 *     }
 * )
 */
class CategoryWithPivotSchema {}
