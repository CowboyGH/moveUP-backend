<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="UserWorkoutSimple",
 *     type="object",
 *     title="Тренировка пользователя (кратко)",
 *     @OA\Property(property="user_workout_id", type="integer", example=926),
 *     @OA\Property(
 *         property="workout",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=76),
 *         @OA\Property(property="title", type="string", example="Базовая силовая тренировка"),
 *         @OA\Property(property="description", type="string", example="Фундаментальные упражнения для развития силы"),
 *         @OA\Property(property="duration_minutes", type="string", example="20"),
 *         @OA\Property(property="type", type="string", example="strength"),
 *         @OA\Property(property="image", type="string", nullable=true, example=null)
 *     ),
 *     @OA\Property(
 *         property="phase",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Подготовительная фаза")
 *     ),
 *     @OA\Property(property="exercises_count", type="integer", example=1),
 *     @OA\Property(property="warmups_count", type="integer", example=0),
 *     @OA\Property(property="status", type="string", enum={"assigned", "started", "completed"}, example="assigned"),
 *     @OA\Property(property="can_be_started", type="boolean", example=true),
 *     @OA\Property(property="is_started", type="boolean", example=false),
 *     @OA\Property(property="started_at", type="string", format="date-time", nullable=true, example=null)
 * )
 */
class WorkoutExecutionSchemas {}

/**
 * @OA\Schema(
 *     schema="WarmupItem",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Мобилизация бедра"),
 *     @OA\Property(property="description", type="string", example="Выполняйте указанное упражнение 60 сек."),
 *     @OA\Property(property="image", type="string", example="http://localhost:8000/storage/warmups/warmup1.jpg"),
 *     @OA\Property(property="duration_seconds", type="integer", example=60),
 *     @OA\Property(property="order_number", type="integer", example=1)
 * )
 */
class WarmupItemSchema {}

/**
 * @OA\Schema(
 *     schema="ExerciseItem",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=21),
 *     @OA\Property(property="title", type="string", example="eos quos error"),
 *     @OA\Property(property="description", type="string", example="Autem sunt dicta omnis sed vel voluptate et."),
 *     @OA\Property(property="image", type="string", example="http://localhost:8000/storage/exercises/exercise-2.jpg"),
 *     @OA\Property(property="sets", type="integer", example=2),
 *     @OA\Property(property="reps", type="integer", example=11),
 *     @OA\Property(property="order_number", type="integer", example=5),
 *     @OA\Property(property="current_weight", type="number", format="float", nullable=true, example=null)
 * )
 */
class ExerciseItemSchema {}

/**
 * @OA\Schema(
 *     schema="WorkoutDetailsResponse",
 *     type="object",
 *     @OA\Property(property="user_workout_id", type="integer", example=926),
 *     @OA\Property(
 *         property="workout",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=76),
 *         @OA\Property(property="title", type="string", example="Базовая силовая тренировка"),
 *         @OA\Property(property="description", type="string", example="Фундаментальные упражнения для развития силы"),
 *         @OA\Property(property="duration_minutes", type="string", example="20"),
 *         @OA\Property(property="type", type="string", example="strength"),
 *         @OA\Property(property="image", type="string", nullable=true, example=null)
 *     ),
 *     @OA\Property(
 *         property="warmups",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/WarmupItem")
 *     ),
 *     @OA\Property(
 *         property="exercises",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/ExerciseItem")
 *     ),
 *     @OA\Property(property="started_at", type="string", format="date-time", nullable=true, example="2026-03-16T05:13:25.000000Z"),
 *     @OA\Property(property="status", type="string", enum={"assigned", "started", "completed"}, example="started")
 * )
 */
class WorkoutDetailsResponseSchema {}

/**
 * @OA\Schema(
 *     schema="NextWarmupResponse",
 *     type="object",
 *     @OA\Property(property="type", type="string", enum={"warmup", "exercise", "completed"}, example="warmup"),
 *     @OA\Property(
 *         property="warmup",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Мобилизация бедра"),
 *         @OA\Property(property="description", type="string", example="Выполняйте указанное упражнение 60 сек."),
 *         @OA\Property(property="image", type="string", example="http://localhost:8000/storage/warmups/warmup1.jpg"),
 *         @OA\Property(property="duration_seconds", type="integer", example=60),
 *         @OA\Property(property="order_number", type="integer", example=1),
 *         @OA\Property(property="is_last", type="boolean", example=true)
 *     )
 * )
 */
class NextWarmupResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ExerciseResponse",
 *     type="object",
 *     @OA\Property(property="type", type="string", enum={"exercise", "completed"}, example="exercise"),
 *     @OA\Property(property="needs_weight_input", type="boolean", example=true),
 *     @OA\Property(
 *         property="exercise",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=21),
 *         @OA\Property(property="title", type="string", example="eos quos error"),
 *         @OA\Property(property="description", type="string", example="Autem sunt dicta omnis sed vel voluptate et."),
 *         @OA\Property(property="image", type="string", example="http://localhost:8000/storage/exercises/exercise-2.jpg"),
 *         @OA\Property(property="sets", type="integer", example=2),
 *         @OA\Property(property="reps", type="integer", example=11),
 *         @OA\Property(property="order_number", type="integer", example=5),
 *         @OA\Property(property="current_weight", type="number", format="float", nullable=true, example=null),
 *         @OA\Property(property="is_last", type="boolean", example=true),
 *         @OA\Property(property="exercise_number", type="integer", example=1),
 *         @OA\Property(property="total_exercises", type="integer", example=1)
 *     )
 * )
 */
class ExerciseResponseSchema {}

/**
 * @OA\Schema(
 *     schema="CompletedResponse",
 *     type="object",
 *     @OA\Property(property="type", type="string", example="completed"),
 *     @OA\Property(property="message", type="string", example="Все упражнения выполнены. Завершите тренировку.")
 * )
 */
class CompletedResponseSchema {}

/**
 * @OA\Schema(
 *     schema="SaveExerciseResultRequest",
 *     type="object",
 *     required={"exercise_id", "reaction"},
 *     @OA\Property(property="exercise_id", type="integer", example=21),
 *     @OA\Property(property="reaction", type="string", enum={"good", "normal", "bad"}, example="good"),
 *     @OA\Property(property="weight_used", type="number", format="float", example=40, nullable=true),
 *     @OA\Property(property="sets_completed", type="integer", example=2, nullable=true),
 *     @OA\Property(property="reps_completed", type="integer", example=11, nullable=true)
 * )
 */
class SaveExerciseResultRequestSchema {}

/**
 * @OA\Schema(
 *     schema="WorkoutReactionAnalysis",
 *     type="object",
 *     @OA\Property(property="pattern", type="string", enum={"no_data", "consistently_good", "consistently_bad", "mostly_good", "mostly_bad", "mixed"}, example="consistently_good"),
 *     @OA\Property(property="consecutive_good", type="integer", example=1),
 *     @OA\Property(property="consecutive_bad", type="integer", example=0),
 *     @OA\Property(property="last_reaction", type="string", enum={"good", "normal", "bad"}, example="good"),
 *     @OA\Property(property="trend", type="string", enum={"neutral", "positive_streak", "negative", "negative_critical"}, example="neutral"),
 *     @OA\Property(
 *         property="stats",
 *         type="object",
 *         @OA\Property(property="good", type="integer", example=1),
 *         @OA\Property(property="normal", type="integer", example=0),
 *         @OA\Property(property="bad", type="integer", example=0),
 *         @OA\Property(property="total", type="integer", example=1)
 *     )
 * )
 */
class WorkoutReactionAnalysisSchema {}

/**
 * @OA\Schema(
 *     schema="WorkoutLoadAdjustment",
 *     type="object",
 *     @OA\Property(property="applied", type="boolean", example=false),
 *     @OA\Property(property="type", type="string", nullable=true, example=null),
 *     @OA\Property(property="percent", type="integer", example=0),
 *     @OA\Property(property="old_weight", type="number", format="float", example=40),
 *     @OA\Property(property="new_weight", type="number", format="float", example=40),
 *     @OA\Property(property="message", type="string", nullable=true, example=null)
 * )
 */
class WorkoutLoadAdjustmentSchema  {}

/**
 * @OA\Schema(
 *     schema="ExerciseResult",
 *     type="object",
 *     @OA\Property(
 *         property="reaction",
 *         type="object",
 *         @OA\Property(property="user_id", type="integer", example=322),
 *         @OA\Property(property="exercise_id", type="integer", example=21),
 *         @OA\Property(property="reaction_date", type="string", format="date", example="2026-03-15T17:00:00.000000Z"),
 *         @OA\Property(property="user_workout_id", type="integer", example=926),
 *         @OA\Property(property="reaction", type="string", enum={"good", "normal", "bad"}, example="good"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2026-03-16T05:15:39.000000Z"),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-16T05:15:39.000000Z"),
 *         @OA\Property(property="id", type="integer", example=31)
 *     ),
 *     @OA\Property(property="analysis", ref="#/components/schemas/WorkoutReactionAnalysis"),
 *     @OA\Property(property="adjustments", ref="#/components/schemas/WorkoutLoadAdjustment"),
 *     @OA\Property(property="rest_phase", type="object", nullable=true, example=null),
 *     @OA\Property(property="current_weight", type="number", format="float", example=40),
 *     @OA\Property(property="recommendations", type="array", @OA\Items(type="string"), example={})
 * )
 */
class ExerciseResultSchema {}

/**
 * @OA\Schema(
 *     schema="SaveExerciseResultResponse",
 *     type="object",
 *     @OA\Property(property="exercise_result", ref="#/components/schemas/ExerciseResult"),
 *     @OA\Property(property="next_url", type="string", example="http://localhost:8000/api/workout-execution/926/next-exercise")
 * )
 */
class SaveExerciseResultResponseSchema {}

/**
 * @OA\Schema(
 *     schema="PhaseProgress",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=73),
 *     @OA\Property(property="user_id", type="integer", example=322),
 *     @OA\Property(property="phase_id", type="integer", example=2),
 *     @OA\Property(property="streak_days", type="integer", example=0),
 *     @OA\Property(property="completed_workouts", type="integer", example=0),
 *     @OA\Property(property="weekly_workout_goal", type="integer", example=4),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-16T05:16:28.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-03-16T05:16:28.000000Z")
 * )
 */
class PhaseProgressSchema {}

/**
 * @OA\Schema(
 *     schema="CompleteWorkoutResponse",
 *     type="object",
 *     @OA\Property(
 *         property="user_workout",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=926),
 *         @OA\Property(property="completed_at", type="string", format="date-time", example="2026-03-16T05:16:28.000000Z")
 *     ),
 *     @OA\Property(property="phase_progress", ref="#/components/schemas/PhaseProgress")
 * )
 */
class CompleteWorkoutResponseSchema {}
