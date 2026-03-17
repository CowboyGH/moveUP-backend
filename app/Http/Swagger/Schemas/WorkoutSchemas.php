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
 *     @OA\Property(property="image_url", type="string", example="http://localhost/storage/exercises/squat.jpg"),
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
 *     @OA\Property(property="image_url", type="string", example="http://localhost/storage/warmups/joint-warmup.jpg"),
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
 *     @OA\Property(property="duration_minutes", type="string", example=30),
 *     @OA\Property(property="image", type="string", nullable=true, example="workouts/morning-workout.jpg"),
 *     @OA\Property(property="image_url", type="string", nullable=true, example="http://localhost/storage/workouts/morning-workout.jpg"),
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
 *     @OA\Property(property="duration_minutes", type="string", example=30, description="Длительность в минутах"),
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

/**
 * @OA\Schema(
 *     schema="WorkoutsListResponse",
 *     type="object",
 *     @OA\Property(
 *         property="assigned",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/UserWorkoutSimple")
 *     ),
 *     @OA\Property(
 *         property="started",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/UserWorkoutSimple")
 *     ),
 *     @OA\Property(property="has_active", type="boolean", example=false)
 * )
 */
class WorkoutsListResponseSchema{}


/**
 * @OA\Schema(
 *     schema="WorkoutStartRequest",
 *     type="object",
 *     required={"workout_id"},
 *     @OA\Property(property="workout_id", type="integer", example=76)
 * )
 */
class WorkoutStartRequestSchema{}

/**
 * @OA\Schema(
 *     schema="WorkoutStartResponse",
 *     type="object",
 *     @OA\Property(property="user_workout_id", type="integer", example=926),
 *     @OA\Property(property="started_at", type="string", format="date-time", example="2026-03-16T05:13:25.000000Z")
 * )
 */
class WorkoutStartResponseSchema{}

/**
 * @OA\Schema(
 *     schema="ActiveWorkoutInfo",
 *     type="object",
 *     title="Информация об активной тренировке",
 *     @OA\Property(property="id", type="integer", example=926),
 *     @OA\Property(property="workout_id", type="integer", example=76),
 *     @OA\Property(property="title", type="string", example="Базовая силовая тренировка"),
 *     @OA\Property(property="started_at", type="string", format="date-time", example="2026-03-16T05:13:25.000000Z"),
 *     @OA\Property(property="duration_minutes", type="integer", example=45)
 * )
 */
class ActiveWorkoutInfoSchema {}

/**
 * @OA\Schema(
 *     schema="WorkoutHistoryStatistics",
 *     type="object",
 *     title="Статистика тренировок",
 *     @OA\Property(property="total_workouts_assigned", type="integer", example=25),
 *     @OA\Property(property="total_workouts_completed", type="integer", example=18),
 *     @OA\Property(property="total_workouts_in_progress", type="integer", example=1),
 *     @OA\Property(property="total_workouts_pending", type="integer", example=6),
 *     @OA\Property(property="last_workout_date", type="string", format="date-time", nullable=true, example="2026-03-15T10:30:00.000000Z")
 * )
 */
class WorkoutHistoryStatisticsSchema {}

/**
 * @OA\Schema(
 *     schema="WorkoutProgress",
 *     type="object",
 *     title="Прогресс тренировки",
 *     @OA\Property(property="exercises_completed", type="integer", example=8),
 *     @OA\Property(property="exercises_total", type="integer", example=10),
 *     @OA\Property(property="warmups_completed", type="integer", example=3),
 *     @OA\Property(property="warmups_total", type="integer", example=3)
 * )
 */
class WorkoutProgressSchema {}

/**
 * @OA\Schema(
 *     schema="WorkoutHistoryItem",
 *     type="object",
 *     title="Элемент истории тренировок",
 *     @OA\Property(property="id", type="integer", example=925),
 *     @OA\Property(
 *         property="workout",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=75),
 *         @OA\Property(property="title", type="string", example="Кардио тренировка")
 *     ),
 *     @OA\Property(property="started_at", type="string", format="date-time", example="2026-03-14T08:20:00.000000Z"),
 *     @OA\Property(property="completed_at", type="string", format="date-time", example="2026-03-14T09:05:00.000000Z"),
 *     @OA\Property(property="status", type="string", enum={"assigned", "started", "completed"}, example="completed"),
 *     @OA\Property(property="duration", type="integer", nullable=true, example=45),
 *     @OA\Property(
 *         property="progress",
 *         ref="#/components/schemas/WorkoutProgress"
 *     )
 * )
 */
class WorkoutHistoryItemSchema {}

/**
 * @OA\Schema(
 *     schema="WorkoutHistoryResponse",
 *     type="object",
 *     title="Ответ с историей тренировок",
 *     @OA\Property(
 *         property="active",
 *         ref="#/components/schemas/ActiveWorkoutInfo",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="statistics",
 *         ref="#/components/schemas/WorkoutHistoryStatistics"
 *     ),
 *     @OA\Property(
 *         property="history",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/WorkoutHistoryItem")
 *     )
 * )
 */
class WorkoutHistoryResponseSchema {}
