<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="UpdateProfileRequest",
 *     type="object",
 *     @OA\Property(property="name", type="string", maxLength=20, example="Иван Петров", description="Только буквы и пробелы"),
 *     @OA\Property(property="email", type="string", format="email", maxLength=255, example="newemail@example.com")
 * )
 */
class ProfileSchemas {}

/**
 * @OA\Schema(
 *     schema="ChangePasswordRequest",
 *     type="object",
 *     required={"old_password", "new_password", "new_password_confirmation"},
 *     @OA\Property(property="old_password", type="string", format="password", example="oldpass123"),
 *     @OA\Property(property="new_password", type="string", format="password", minLength=8, maxLength=12, example="newpass123", description="Только латинские буквы и цифры"),
 *     @OA\Property(property="new_password_confirmation", type="string", format="password", example="newpass123")
 * )
 */
class ChangePasswordRequestSchema {}

/**
 * @OA\Schema(
 *     schema="UserInProfile",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Иван Иванов"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="avatar_url", type="string", nullable=true, example="http://localhost:8000/storage/avatars/EEWWelWPSfLlZE54ZROgfa6Qwryb4HQSenpNJjKd.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-12 16:15:47"),
 *     @OA\Property(property="email_verified", type="boolean", example=true)
 * )
 */
class UserInProfileSchema {}

/**
 * @OA\Schema(
 *     schema="UserParametersInProfile",
 *     type="object",
 *     nullable=true,
 *     @OA\Property(property="goal", type="string", example="Общее укрепление организма"),
 *     @OA\Property(property="level", type="string", example="Начинающий"),
 *     @OA\Property(property="equipment", type="string", example="Смешанное"),
 *     @OA\Property(property="height", type="integer", example=199),
 *     @OA\Property(property="weight", type="integer", example=78),
 *     @OA\Property(property="age", type="integer", example=20),
 *     @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male")
 * )
 */
class UserParametersInProfileSchema {}

/**
 * @OA\Schema(
 *     schema="SubscriptionHistoryItem",
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
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"active", "expired", "cancelled", "inactive"},
 *         example="active"
 *     )
 * )
 */
class SubscriptionHistoryItemSchema {}

/**
 * @OA\Schema(
 *     schema="ActiveSubscriptionInProfile",
 *     type="object",
 *     nullable=true,
 *     @OA\Property(property="id", type="integer", example=21),
 *     @OA\Property(property="name", type="string", example="3 месяца"),
 *     @OA\Property(property="price", type="string", example="1400.00"),
 *     @OA\Property(property="start_date", type="string", format="date", example="2026-03-15"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2026-06-13"),
 *     @OA\Property(property="days_left", type="number", format="float", example=89.38)
 * )
 */
class ActiveSubscriptionInProfileSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileSubscriptions",
 *     type="object",
 *     @OA\Property(property="active", ref="#/components/schemas/ActiveSubscriptionInProfile"),
 *     @OA\Property(
 *         property="history",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/SubscriptionHistoryItem")
 *     )
 * )
 */
class ProfileSubscriptionsSchema {}

/**
 * @OA\Schema(
 *     schema="CardInProfile",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="card_holder", type="string", example="Connie Hammes"),
 *     @OA\Property(property="card_last_four", type="string", example="7065"),
 *     @OA\Property(property="expiry_month", type="string", example="12"),
 *     @OA\Property(property="expiry_year", type="string", example="2027"),
 *     @OA\Property(property="expiry_formatted", type="string", example="12/2027"),
 *     @OA\Property(property="is_default", type="boolean", example=true)
 * )
 */
class CardInProfileSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileCurrentPhase",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=2),
 *     @OA\Property(property="name", type="string", example="Базовая фаза"),
 *     @OA\Property(property="description", type="string", example="Формирование базовых навыков и силы. Увеличение рабочих весов и освоение правильной техники выполнения упражнений."),
 *     @OA\Property(property="duration_days", type="integer", example=14),
 *     @OA\Property(property="order_number", type="integer", example=2)
 * )
 */
class ProfileCurrentPhaseSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileProgress",
 *     type="object",
 *     @OA\Property(property="streak_days", type="integer", example=19),
 *     @OA\Property(property="completed_workouts", type="integer", example=8),
 *     @OA\Property(property="days_passed", type="integer", example=26),
 *     @OA\Property(property="days_left", type="integer", example=0),
 *     @OA\Property(property="expected_workouts", type="integer", example=15),
 *     @OA\Property(property="total_expected_workouts", type="integer", example=8),
 *     @OA\Property(property="weekly_goal", type="integer", example=4),
 *     @OA\Property(property="phase_started_at", type="string", format="date-time", example="2026-02-17T16:10:12.000000Z"),
 *     @OA\Property(property="last_workout_date", type="string", format="date-time", example="2026-03-10T16:52:31.000000Z"),
 *     @OA\Property(property="has_workout_today", type="boolean", example=false)
 * )
 */
class ProfileProgressSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileNextPhase",
 *     type="object",
 *     nullable=true,
 *     @OA\Property(property="id", type="integer", example=3),
 *     @OA\Property(property="name", type="string", example="Интенсивная фаза"),
 *     @OA\Property(property="order_number", type="integer", example=3)
 * )
 */
class ProfileNextPhaseSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileRecentWorkout",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="workout_name", type="string", example="Функциональный тренинг"),
 *     @OA\Property(property="completed_at", type="string", format="date-time", example="2026-03-10T16:52:31.000000Z"),
 *     @OA\Property(property="duration", type="number", format="float", example=1610.87)
 * )
 */
class ProfileRecentWorkoutSchema {}

/**
 * @OA\Schema(
 *     schema="ProfilePhase",
 *     type="object",
 *     @OA\Property(property="has_progress", type="boolean", example=true),
 *     @OA\Property(property="current_phase", ref="#/components/schemas/ProfileCurrentPhase"),
 *     @OA\Property(property="progress", ref="#/components/schemas/ProfileProgress"),
 *     @OA\Property(property="next_phase", ref="#/components/schemas/ProfileNextPhase"),
 *     @OA\Property(
 *         property="recent_workouts",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/ProfileRecentWorkout")
 *     ),
 *     @OA\Property(property="can_advance", type="boolean", example=true)
 * )
 */
class ProfilePhaseSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="success"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="user", ref="#/components/schemas/UserInProfile"),
 *         @OA\Property(property="parameters", ref="#/components/schemas/UserParametersInProfile"),
 *         @OA\Property(property="subscriptions", ref="#/components/schemas/ProfileSubscriptions"),
 *         @OA\Property(property="phase", ref="#/components/schemas/ProfilePhase"),
 *         @OA\Property(
 *             property="cards",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/CardInProfile")
 *         ),
 *         @OA\Property(
 *             property="statistics",
 *             type="array",
 *             description="Пустой массив, статистика еще не реализована",
 *             @OA\Items(type="object"),
 *             example={}
 *         )
 *     )
 * )
 */
class ProfileResponseSchema {}

/**
 * @OA\Schema(
 *     schema="UpdateProfileResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Профиль успешно обновлен"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="name", type="string", example="Иван Петров"),
 *         @OA\Property(property="email", type="string", format="email", example="newemail@example.com"),
 *         @OA\Property(property="avatar", type="string", nullable=true, example="http://localhost/storage/avatars/avatar_1_1234567890.jpg")
 *     )
 * )
 */
class UpdateProfileResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ChangePasswordResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Пароль успешно изменен")
 * )
 */
class ChangePasswordResponseSchema {}

/**
 * @OA\Schema(
 *     schema="DeleteProfileResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Профиль успешно удален")
 * )
 */
class DeleteProfileResponseSchema {}

/**
 * @OA\Schema(
 *     schema="StatisticsResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         description="Данные статистики (заглушка)",
 *         example={}
 *     )
 * )
 */
class StatisticsResponseSchema {}

/**
 * @OA\Schema(
 *     schema="InvalidCurrentPasswordResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="code", example="validation_failed"),
 *             @OA\Property(property="message", example="Неверный текущий пароль")
 *         )
 *     }
 * )
 */
class InvalidCurrentPasswordResponseSchema {}
