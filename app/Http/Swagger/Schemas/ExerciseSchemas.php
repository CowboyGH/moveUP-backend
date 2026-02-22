<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Equipment",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Гантели")
 * )
 */
class ExerciseSchemas {}

/**
 * @OA\Schema(
 *     schema="Exercise",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Жим гантелей лежа"),
 *     @OA\Property(property="description", type="string", example="Базовое упражнение для развития грудных мышц"),
 *     @OA\Property(property="image", type="string", example="/uploads/exercises/dumbbell-press.jpg"),
 *     @OA\Property(property="muscle_group", type="string", example="Грудные"),
 *     @OA\Property(
 *         property="equipment",
 *         ref="#/components/schemas/Equipment",
 *         nullable=true
 *     ),
 *     @OA\Property(property="workouts_count", type="integer", example=5),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 * )
 */
class ExerciseSchema {}

/**
 * @OA\Schema(
 *     schema="ExerciseWithWorkouts",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Exercise"),
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
 *                         @OA\Property(property="sets", type="integer", example=3),
 *                         @OA\Property(property="reps", type="integer", example=12),
 *                         @OA\Property(property="order_number", type="integer", example=1)
 *                     )
 *                 )
 *             )
 *         )
 *     }
 * )
 */
class ExerciseWithWorkoutsSchema {}

/**
 * @OA\Schema(
 *     schema="StoreExerciseRequest",
 *     type="object",
 *     required={"equipment_id", "title", "description", "image", "muscle_group"},
 *     @OA\Property(property="equipment_id", type="integer", example=1, description="ID оборудования"),
 *     @OA\Property(property="title", type="string", example="Жим гантелей лежа", description="Название упражнения"),
 *     @OA\Property(property="description", type="string", example="Базовое упражнение для развития грудных мышц", description="Описание упражнения"),
 *     @OA\Property(property="image", type="string", example="/uploads/exercises/dumbbell-press.jpg", description="Путь к изображению"),
 *     @OA\Property(property="muscle_group", type="string", example="Грудные", description="Группа мышц")
 * )
 */
class StoreExerciseRequestSchema {}

/**
 * @OA\Schema(
 *     schema="UpdateExerciseRequest",
 *     type="object",
 *     @OA\Property(property="equipment_id", type="integer", example=1, description="ID оборудования"),
 *     @OA\Property(property="title", type="string", example="Жим гантелей лежа", description="Название упражнения"),
 *     @OA\Property(property="description", type="string", example="Базовое упражнение для развития грудных мышц", description="Описание упражнения"),
 *     @OA\Property(property="image", type="string", example="/uploads/exercises/dumbbell-press.jpg", description="Путь к изображению"),
 *     @OA\Property(property="muscle_group", type="string", example="Грудные", description="Группа мышц")
 * )
 */
class UpdateExerciseRequestSchema {}
