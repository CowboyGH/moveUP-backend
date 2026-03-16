<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="PhaseSimple",
 *     type="object",
 *     title="Фаза (полная)",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Подготовительная фаза"),
 *     @OA\Property(property="description", type="string", example="Начальный этап для адаптации к тренировкам."),
 *     @OA\Property(property="duration_days", type="integer", example=7),
 *     @OA\Property(property="order_number", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-05T07:29:11.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-03-05T07:29:11.000000Z")
 * )
 */
class PhaseSchemas {}

/**
 * @OA\Schema(
 *     schema="PhaseBrief",
 *     type="object",
 *     title="Фаза (кратко, для текущей фазы)",
 *     @OA\Property(property="id", type="integer", example=4),
 *     @OA\Property(property="name", type="string", example="Фаза отдыха"),
 *     @OA\Property(property="description", type="string", example="Восстановление и легкие тренировки."),
 *     @OA\Property(property="duration_days", type="integer", example=7),
 *     @OA\Property(property="order_number", type="integer", example=4)
 * )
 */
class PhaseBriefSchema {}

/**
 * @OA\Schema(
 *     schema="PhaseNext",
 *     type="object",
 *     title="Следующая фаза",
 *     @OA\Property(property="id", type="integer", example=5),
 *     @OA\Property(property="name", type="string", example="Продвинутая фаза"),
 *     @OA\Property(property="order_number", type="integer", example=5)
 * )
 */
class PhaseNextSchema {}

/**
 * @OA\Schema(
 *     schema="WorkoutSimple",
 *     type="object",
 *     title="Тренировка (кратко)",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="phase_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="ОФП тренировка"),
 *     @OA\Property(property="description", type="string", example="Жимы, тяги и подтягивания для верхней части тела"),
 *     @OA\Property(property="duration_minutes", type="string", example="32"),
 *     @OA\Property(property="type", type="string", example="general"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="image", type="string", nullable=true, example="workouts/morning-workout.jpg"),
 *     @OA\Property(property="image_url", type="string", nullable=true, example="http://localhost/storage/workouts/morning-workout.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-05T07:29:23.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-03-05T07:29:23.000000Z")
 * )
 */
class WorkoutSimpleSchema {}

/**
 * @OA\Schema(
 *     schema="PhaseWithWorkouts",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/PhaseSimple"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="workouts",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/WorkoutSimple")
 *             )
 *         )
 *     }
 * )
 */
class PhaseWithWorkoutsSchema {}

/**
 * @OA\Schema(
 *     schema="UserPhaseProgress",
 *     type="object",
 *     title="Прогресс пользователя по фазам",
 *     @OA\Property(property="has_progress", type="boolean", example=true),
 *     @OA\Property(
 *         property="current_phase",
 *         ref="#/components/schemas/PhaseBrief"
 *     ),
 *     @OA\Property(
 *         property="progress",
 *         type="object",
 *         @OA\Property(property="streak_days", type="integer", example=3),
 *         @OA\Property(property="completed_workouts", type="integer", example=2),
 *         @OA\Property(property="days_passed", type="integer", example=4),
 *         @OA\Property(property="days_left", type="integer", example=3),
 *         @OA\Property(property="expected_workouts", type="integer", example=3),
 *         @OA\Property(property="total_expected_workouts", type="integer", example=6),
 *         @OA\Property(property="weekly_goal", type="integer", example=6),
 *         @OA\Property(property="phase_started_at", type="string", format="date-time", example="2026-03-05T07:45:30.000000Z"),
 *         @OA\Property(property="last_workout_date", type="string", format="date-time", nullable=true, example=null),
 *         @OA\Property(property="has_workout_today", type="boolean", example=false)
 *     ),
 *     @OA\Property(
 *         property="next_phase",
 *         ref="#/components/schemas/PhaseNext",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="recent_workouts",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=42),
 *             @OA\Property(property="workout_name", type="string", example="Утренняя зарядка"),
 *             @OA\Property(property="completed_at", type="string", format="date-time", example="2026-02-28T08:00:00Z"),
 *             @OA\Property(property="duration", type="integer", example=35)
 *         )
 *     ),
 *     @OA\Property(property="can_advance", type="boolean", example=true)
 * )
 */
class UserPhaseProgressSchema {}

/**
 * @OA\Schema(
 *     schema="PhaseList",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/PhaseSimple")
 * )
 */
class PhaseListSchema {}

/**
 * @OA\Schema(
 *     schema="UpdateWeeklyGoalRequest",
 *     type="object",
 *     required={"weekly_goal"},
 *     @OA\Property(property="weekly_goal", type="integer", minimum=1, maximum=7, example=5)
 * )
 */
class UpdateWeeklyGoalRequestSchema {}

/**
 * @OA\Schema(
 *     schema="UpdateWeeklyGoalResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Недельная цель обновлена"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="weekly_goal", type="integer", example=5)
 *     )
 * )
 */
class UpdateWeeklyGoalResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ValidationErrorWeeklyGoal",
 *     type="object",
 *     @OA\Property(property="code", type="string", example="validation_failed"),
 *     @OA\Property(property="message", type="string", example="Ошибка валидации"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\Property(
 *             property="weekly_goal",
 *             type="array",
 *             @OA\Items(type="string", example="The weekly goal field is required.")
 *         )
 *     )
 * )
 */
class ValidationErrorWeeklyGoalSchema {}
