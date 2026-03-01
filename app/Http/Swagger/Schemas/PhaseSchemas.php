<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="PhaseSimple",
 *     type="object",
 *     title="Фаза (кратко)",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Подготовительная фаза"),
 *     @OA\Property(property="order_number", type="integer", example=1)
 * )
 */
class PhaseSchemas {}

/**
 * @OA\Schema(
 *     schema="PhaseDetails",
 *     type="object",
 *     title="Детальная информация о фазе",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Phase"),
 *         @OA\Schema(
 *             @OA\Property(property="description", type="string", example="Начальный этап для адаптации к тренировкам"),
 *             @OA\Property(property="duration_days", type="integer", example=7),
 *             @OA\Property(property="min_workouts", type="integer", example=3)
 *         )
 *     }
 * )
 */
class PhaseDetails {}

/**
 * @OA\Schema(
 *     schema="UserPhaseProgress",
 *     type="object",
 *     title="Прогресс пользователя по фазам",
 *     @OA\Property(property="has_progress", type="boolean", example=true),
 *     @OA\Property(
 *         property="current_phase",
 *         ref="#/components/schemas/PhaseDetails"
 *     ),
 *     @OA\Property(
 *         property="progress",
 *         type="object",
 *         @OA\Property(property="streak_days", type="integer", example=3),
 *         @OA\Property(property="completed_workouts", type="integer", example=2),
 *         @OA\Property(property="days_passed", type="integer", example=4),
 *         @OA\Property(property="days_left", type="integer", example=3),
 *         @OA\Property(property="workouts_left", type="integer", example=1),
 *         @OA\Property(property="phase_started_at", type="string", format="date-time", example="2026-02-25T10:00:00Z"),
 *         @OA\Property(property="last_workout_date", type="string", format="date-time", example="2026-02-28T15:30:00Z"),
 *         @OA\Property(property="has_workout_today", type="boolean", example=false)
 *     ),
 *     @OA\Property(
 *         property="next_phase",
 *         ref="#/components/schemas/Phase",
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
 *     @OA\Property(property="can_advance", type="boolean", example=false)
 * )
 */
class UserPhaseProgress {}

/**
 * @OA\Schema(
 *     schema="PhaseList",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/PhaseDetails")
 * )
 */
class PhaseList {}
