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
 *     @OA\Property(property="avatar_url", type="string", nullable=true, example="http://localhost/storage/avatars/avatar_1_1234567890.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01 12:00:00"),
 *     @OA\Property(property="email_verified", type="boolean", example=true)
 * )
 */
class UserInProfileSchema {}

/**
 * @OA\Schema(
 *     schema="UserParametersInProfile",
 *     type="object",
 *     nullable=true,
 *     @OA\Property(property="goal", type="string", example="Рост силовых показателей"),
 *     @OA\Property(property="level", type="string", example="Средний"),
 *     @OA\Property(property="equipment", type="string", example="Зал"),
 *     @OA\Property(property="height", type="integer", example=175),
 *     @OA\Property(property="weight", type="number", format="float", example=70.5),
 *     @OA\Property(property="age", type="integer", example=25),
 *     @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male")
 * )
 */
class UserParametersInProfileSchema {}

/**
 * @OA\Schema(
 *     schema="ActiveSubscriptionInProfile",
 *     type="object",
 *     nullable=true,
 *     @OA\Property(property="id", type="integer", example=5),
 *     @OA\Property(property="name", type="string", example="Премиум"),
 *     @OA\Property(property="price", type="number", format="float", example=29.99),
 *     @OA\Property(property="start_date", type="string", format="date", example="2024-01-01"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2024-02-01"),
 *     @OA\Property(property="days_left", type="integer", example=15)
 * )
 */
class ActiveSubscriptionInProfileSchema {}

/**
 * @OA\Schema(
 *     schema="CardInProfile",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=3),
 *     @OA\Property(property="card_holder", type="string", example="IVAN IVANOV"),
 *     @OA\Property(property="card_last_four", type="string", example="4242"),
 *     @OA\Property(property="expiry_month", type="string", example="12"),
 *     @OA\Property(property="expiry_year", type="string", example="2025"),
 *     @OA\Property(property="expiry_formatted", type="string", example="12/2025"),
 *     @OA\Property(property="is_default", type="boolean", example=true)
 * )
 */
class CardInProfileSchema {}

/**
 * @OA\Schema(
 *     schema="StatisticsVolumeMonth",
 *     type="object",
 *     @OA\Property(property="month", type="string", example="2024-01"),
 *     @OA\Property(property="value", type="integer", example=320)
 * )
 */
class StatisticsVolumeMonthSchema {}

/**
 * @OA\Schema(
 *     schema="StatisticsVolume",
 *     type="object",
 *     @OA\Property(property="total", type="integer", example=1250),
 *     @OA\Property(
 *         property="by_month",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/StatisticsVolumeMonth")
 *     )
 * )
 */
class StatisticsVolumeSchema {}

/**
 * @OA\Schema(
 *     schema="StatisticsFrequency",
 *     type="object",
 *     @OA\Property(property="total_workouts", type="integer", example=24),
 *     @OA\Property(property="average_per_week", type="number", format="float", example=3.2),
 *     @OA\Property(property="current_streak", type="integer", example=5),
 *     @OA\Property(property="max_streak", type="integer", example=12)
 * )
 */
class StatisticsFrequencySchema {}

/**
 * @OA\Schema(
 *     schema="StatisticsTrend",
 *     type="object",
 *     @OA\Property(property="direction", type="string", enum={"up", "down", "stable"}, example="up"),
 *     @OA\Property(property="percentage", type="integer", example=15),
 *     @OA\Property(property="compared_to", type="string", example="last_month")
 * )
 */
class StatisticsTrendSchema {}

/**
 * @OA\Schema(
 *     schema="StatisticsCategory",
 *     type="object",
 *     @OA\Property(property="name", type="string", example="Силовые"),
 *     @OA\Property(property="count", type="integer", example=15),
 *     @OA\Property(property="percentage", type="number", format="float", example=62.5)
 * )
 */
class StatisticsCategorySchema {}

/**
 * @OA\Schema(
 *     schema="StatisticsData",
 *     type="object",
 *     @OA\Property(property="volume", ref="#/components/schemas/StatisticsVolume"),
 *     @OA\Property(property="frequency", ref="#/components/schemas/StatisticsFrequency"),
 *     @OA\Property(property="trend", ref="#/components/schemas/StatisticsTrend"),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/StatisticsCategory")
 *     )
 * )
 */
class StatisticsDataSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Профиль получен"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="user", ref="#/components/schemas/UserInProfile"),
 *         @OA\Property(property="parameters", ref="#/components/schemas/UserParametersInProfile"),
 *         @OA\Property(
 *             property="subscriptions",
 *             type="object",
 *             @OA\Property(property="active", ref="#/components/schemas/ActiveSubscriptionInProfile"),
 *             @OA\Property(property="history", type="array", @OA\Items(type="object"))
 *         ),
 *         @OA\Property(property="phase", type="object", description="Данные о текущей фазе"),
 *         @OA\Property(
 *             property="cards",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/CardInProfile")
 *         ),
 *         @OA\Property(property="statistics", ref="#/components/schemas/StatisticsData")
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
 *     @OA\Property(property="data", ref="#/components/schemas/StatisticsData")
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
