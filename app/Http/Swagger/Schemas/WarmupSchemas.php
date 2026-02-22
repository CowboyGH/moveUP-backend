<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Warmup",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Суставная гимнастика"),
 *     @OA\Property(property="description", type="string", example="Разминка для подготовки суставов к нагрузке"),
 *     @OA\Property(property="image", type="string", example="/uploads/warmups/joint-gymnastics.jpg"),
 *     @OA\Property(property="workouts_count", type="integer", example=8),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 * )
 */
class WarmupSchemas {}

/**
 * @OA\Schema(
 *     schema="WarmupWithWorkouts",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Warmup"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="workouts",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=10),
 *                     @OA\Property(property="title", type="string", example="Силовая тренировка"),
 *                     @OA\Property(property="description", type="string", example="Тренировка на все группы мышц"),
 *                     @OA\Property(property="duration_minutes", type="string", example="45-60 минут"),
 *                     @OA\Property(property="is_active", type="boolean", example=true),
 *                     @OA\Property(
 *                         property="pivot",
 *                         type="object",
 *                         @OA\Property(property="order_number", type="integer", example=1)
 *                     )
 *                 )
 *             )
 *         )
 *     }
 * )
 */
class WarmupWithWorkoutsSchema {}

/**
 * @OA\Schema(
 *     schema="StoreWarmupRequest",
 *     type="object",
 *     required={"name", "description", "image"},
 *     @OA\Property(property="name", type="string", example="Суставная гимнастика", description="Название разминки"),
 *     @OA\Property(property="description", type="string", example="Разминка для подготовки суставов к нагрузке", description="Описание разминки"),
 *     @OA\Property(property="image", type="string", example="/uploads/warmups/joint-gymnastics.jpg", description="Путь к изображению")
 * )
 */
class StoreWarmupRequestSchema {}

/**
 * @OA\Schema(
 *     schema="UpdateWarmupRequest",
 *     type="object",
 *     @OA\Property(property="name", type="string", example="Суставная гимнастика", description="Название разминки"),
 *     @OA\Property(property="description", type="string", example="Разминка для подготовки суставов к нагрузке", description="Описание разминки"),
 *     @OA\Property(property="image", type="string", example="/uploads/warmups/joint-gymnastics.jpg", description="Путь к изображению")
 * )
 */
class UpdateWarmupRequestSchema {}
