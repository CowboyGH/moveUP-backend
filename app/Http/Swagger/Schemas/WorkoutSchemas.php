<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Phase",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Начальный уровень")
 * )
 */
class WorkoutSchemas {}

/**
 * @OA\Schema(
 *     schema="WorkoutExercise",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Приседания"),
 *     @OA\Property(property="description", type="string", example="Техника выполнения приседаний"),
 *     @OA\Property(property="image", type="string", example="/uploads/exercises/squat.jpg"),
 *     @OA\Property(property="muscle_group", type="string", example="Ноги"),
 *     @OA\Property(
 *         property="equipment",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Штанга")
 *     ),
 *     @OA\Property(
 *         property="pivot",
 *         type="object",
 *         @OA\Property(property="sets", type="integer", example=3),
 *         @OA\Property(property="reps", type="string", example="12"),
 *         @OA\Property(property="order_number", type="integer", example=1)
 *     )
 * )
 */
class WorkoutExerciseSchema {}

/**
 * @OA\Schema(
 *     schema="WorkoutWarmup",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Разминка суставов"),
 *     @OA\Property(property="description", type="string", example="Подготовка суставов к нагрузке"),
 *     @OA\Property(property="image", type="string", example="/uploads/warmups/joint-warmup.jpg"),
 *     @OA\Property(
 *         property="pivot",
 *         type="object",
 *         @OA\Property(property="order_number", type="integer", example=1)
 *     )
 * )
 */
class WorkoutWarmupSchema {}

/**
 * @OA\Schema(
 *     schema="Workout",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Утренняя зарядка"),
 *     @OA\Property(property="description", type="string", example="Комплекс упражнений для пробуждения"),
 *     @OA\Property(property="duration_minutes", type="integer", example=30),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(
 *         property="phase",
 *         ref="#/components/schemas/Phase",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="exercises",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/WorkoutExercise")
 *     ),
 *     @OA\Property(
 *         property="warmups",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/WorkoutWarmup")
 *     ),
 *     @OA\Property(property="user_workouts_count", type="integer", example=15),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 * )
 */
class WorkoutSchema {}

/**
 * @OA\Schema(
 *     schema="StoreWorkoutRequest",
 *     type="object",
 *     required={"title", "description", "duration_minutes"},
 *     @OA\Property(property="phase_id", type="integer", nullable=true, example=1, description="ID фазы"),
 *     @OA\Property(property="title", type="string", example="Утренняя зарядка", description="Название тренировки"),
 *     @OA\Property(property="description", type="string", example="Комплекс упражнений для пробуждения", description="Описание тренировки"),
 *     @OA\Property(property="duration_minutes", type="integer", example=30, description="Длительность в минутах"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Активность тренировки"),
 *     @OA\Property(
 *         property="exercises",
 *         type="array",
 *         description="Список упражнений",
 *         @OA\Items(
 *             type="object",
 *             required={"exercise_id", "sets", "reps", "order_number"},
 *             @OA\Property(property="exercise_id", type="integer", example=1, description="ID упражнения"),
 *             @OA\Property(property="sets", type="integer", example=3, description="Количество подходов"),
 *             @OA\Property(property="reps", type="string", example="12", description="Количество повторений"),
 *             @OA\Property(property="order_number", type="integer", example=1, description="Порядковый номер")
 *         )
 *     ),
 *     @OA\Property(
 *         property="warmups",
 *         type="array",
 *         description="Список разминок",
 *         @OA\Items(
 *             type="object",
 *             required={"warmup_id", "order_number"},
 *             @OA\Property(property="warmup_id", type="integer", example=1, description="ID разминки"),
 *             @OA\Property(property="order_number", type="integer", example=1, description="Порядковый номер")
 *         )
 *     )
 * )
 */
class StoreWorkoutRequestSchema {}

/**
 * @OA\Schema(
 *     schema="UpdateWorkoutRequest",
 *     type="object",
 *     @OA\Property(property="phase_id", type="integer", nullable=true, example=1, description="ID фазы"),
 *     @OA\Property(property="title", type="string", example="Утренняя зарядка", description="Название тренировки"),
 *     @OA\Property(property="description", type="string", example="Комплекс упражнений для пробуждения", description="Описание тренировки"),
 *     @OA\Property(property="duration_minutes", type="integer", example=30, description="Длительность в минутах"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Активность тренировки"),
 *     @OA\Property(
 *         property="exercises",
 *         type="array",
 *         description="Список упражнений",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="exercise_id", type="integer", example=1, description="ID упражнения"),
 *             @OA\Property(property="sets", type="integer", example=3, description="Количество подходов"),
 *             @OA\Property(property="reps", type="string", example="12", description="Количество повторений"),
 *             @OA\Property(property="order_number", type="integer", example=1, description="Порядковый номер")
 *         )
 *     ),
 *     @OA\Property(
 *         property="warmups",
 *         type="array",
 *         description="Список разминок",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="warmup_id", type="integer", example=1, description="ID разминки"),
 *             @OA\Property(property="order_number", type="integer", example=1, description="Порядковый номер")
 *         )
 *     )
 * )
 */
class UpdateWorkoutRequestSchema {}
