<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="ProfileUser",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=292),
 *     @OA\Property(property="name", type="string", example="Иван Иванов"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="avatar_url", type="string", nullable=true, example="http://localhost:8000/storage/avatars/avatar.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-12 16:15:47"),
 *     @OA\Property(property="email_verified", type="boolean", example=true)
 * )
 */
class ProfileDetailSchemas {}

/**
 * @OA\Schema(
 *     schema="ProfileActiveSubscription",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=21),
 *     @OA\Property(property="name", type="string", example="3 месяца"),
 *     @OA\Property(property="price", type="string", example="1400.00"),
 *     @OA\Property(property="start_date", type="string", format="date", example="2026-03-15"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2026-06-13"),
 *     @OA\Property(property="days_left", type="number", format="float", example=89.38)
 * )
 */
class ProfileActiveSubscriptionSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileCard",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="card_holder", type="string", example="IVAN IVANOV"),
 *     @OA\Property(property="card_last_four", type="string", example="4242"),
 *     @OA\Property(property="expiry_month", type="string", example="12"),
 *     @OA\Property(property="expiry_year", type="string", example="2025"),
 *     @OA\Property(property="expiry_formatted", type="string", example="12/2025"),
 *     @OA\Property(property="is_default", type="boolean", example=true)
 * )
 */
class ProfileCardSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileUserParameters",
 *     type="object",
 *     @OA\Property(property="goal", type="string", example="Общее укрепление организма"),
 *     @OA\Property(property="level", type="string", example="Начинающий"),
 *     @OA\Property(property="equipment", type="string", example="Смешанное"),
 *     @OA\Property(property="height", type="integer", example=199),
 *     @OA\Property(property="weight", type="integer", example=78),
 *     @OA\Property(property="age", type="integer", example=20),
 *     @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male")
 * )
 */
class ProfileUserParametersSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileHistorySubscription",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=21),
 *     @OA\Property(
 *         property="subscription",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=2),
 *         @OA\Property(property="name", type="string", example="3 месяца"),
 *         @OA\Property(property="price", type="string", example="1400.00")
 *     ),
 *     @OA\Property(property="start_date", type="string", format="date", example="2026-03-15"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2026-06-13"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="status", type="string", enum={"active", "expired", "cancelled", "inactive"}, example="active")
 * )
 */
class ProfileHistorySubscriptionSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileHistoryWorkout",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(
 *         property="workout",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=5),
 *         @OA\Property(property="title", type="string", example="Утренняя зарядка")
 *     ),
 *     @OA\Property(property="completed_at", type="string", format="date-time", example="2026-03-15 10:30:00"),
 *     @OA\Property(property="duration_minutes", type="integer", nullable=true, example=45)
 * )
 */
class ProfileHistoryWorkoutSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileHistoryTest",
 *     type="object",
 *     @OA\Property(property="attempt_id", type="integer", example=3),
 *     @OA\Property(
 *         property="testing",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=2),
 *         @OA\Property(property="title", type="string", example="Базовый тест")
 *     ),
 *     @OA\Property(property="completed_at", type="string", format="date-time", example="2026-03-14 15:20:00"),
 *     @OA\Property(property="pulse", type="integer", example=120),
 *     @OA\Property(property="exercises_count", type="integer", example=5)
 * )
 */
class ProfileHistoryTestSchema {}

/**
 * @OA\Schema(
 *     schema="ProfilePhaseData",
 *     type="object",
 *     description="Данные о текущей фазе и прогрессе пользователя",
 *     @OA\Property(property="has_progress", type="boolean", example=true),
 *     @OA\Property(
 *         property="current_phase",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=2),
 *         @OA\Property(property="name", type="string", example="Базовая фаза"),
 *         @OA\Property(property="description", type="string", example="Формирование базовых навыков и силы."),
 *         @OA\Property(property="duration_days", type="integer", example=14),
 *         @OA\Property(property="order_number", type="integer", example=2)
 *     ),
 *     @OA\Property(
 *         property="progress",
 *         type="object",
 *         @OA\Property(property="streak_days", type="integer", example=19),
 *         @OA\Property(property="completed_workouts", type="integer", example=8),
 *         @OA\Property(property="days_passed", type="integer", example=26),
 *         @OA\Property(property="days_left", type="integer", example=0),
 *         @OA\Property(property="expected_workouts", type="integer", example=15),
 *         @OA\Property(property="total_expected_workouts", type="integer", example=8),
 *         @OA\Property(property="weekly_goal", type="integer", example=4),
 *         @OA\Property(property="phase_started_at", type="string", format="date-time", example="2026-02-17T16:10:12.000000Z"),
 *         @OA\Property(property="last_workout_date", type="string", format="date-time", example="2026-03-10T16:52:31.000000Z"),
 *         @OA\Property(property="has_workout_today", type="boolean", example=false)
 *     ),
 *     @OA\Property(
 *         property="next_phase",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=3),
 *         @OA\Property(property="name", type="string", example="Интенсивная фаза"),
 *         @OA\Property(property="order_number", type="integer", example=3)
 *     ),
 *     @OA\Property(
 *         property="recent_workouts",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="workout_name", type="string", example="Функциональный тренинг"),
 *             @OA\Property(property="completed_at", type="string", format="date-time", example="2026-03-10T16:52:31.000000Z"),
 *             @OA\Property(property="duration", type="number", format="float", example=1610.87)
 *         )
 *     ),
 *     @OA\Property(property="can_advance", type="boolean", example=true)
 * )
 */
class ProfilePhaseDataSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileUserResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="success"),
 *     @OA\Property(property="data", ref="#/components/schemas/ProfileUser")
 * )
 */
class ProfileUserResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileActiveSubscriptionResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="success"),
 *     @OA\Property(property="data", ref="#/components/schemas/ProfileActiveSubscription")
 * )
 */
class ProfileActiveSubscriptionResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileCardsResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="success"),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProfileCard"))
 * )
 */
class ProfileCardsResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileUserParametersResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="success"),
 *     @OA\Property(property="data", ref="#/components/schemas/ProfileUserParameters")
 * )
 */
class ProfileUserParametersResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileHistoryResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="success"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="subscriptions", type="array", @OA\Items(ref="#/components/schemas/ProfileHistorySubscription")),
 *         @OA\Property(property="workouts", type="array", @OA\Items(ref="#/components/schemas/ProfileHistoryWorkout")),
 *         @OA\Property(property="tests", type="array", @OA\Items(ref="#/components/schemas/ProfileHistoryTest"))
 *     )
 * )
 */
class ProfileHistoryResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileEmptyHistoryResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="success"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="message", type="string", example="У пользователя пока нет истории"),
 *         @OA\Property(property="subscriptions", type="array", @OA\Items(type="object")),
 *         @OA\Property(property="workouts", type="array", @OA\Items(type="object")),
 *         @OA\Property(property="tests", type="array", @OA\Items(type="object"))
 *     )
 * )
 */
class ProfileEmptyHistoryResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ProfilePhaseResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="success"),
 *     @OA\Property(property="data", ref="#/components/schemas/ProfilePhaseData")
 * )
 */
class ProfilePhaseResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileIncompleteParametersResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="success"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="message", type="string", example="Параметры пользователя заполнены не полностью")
 *     )
 * )
 */
class ProfileIncompleteParametersResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileEmptyResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="success"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="message", type="string", example="У пользователя нет активной подписки")
 *     )
 * )
 */
class ProfileEmptyResponseSchema {}
