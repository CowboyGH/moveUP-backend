<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="ProfilePhaseProgress",
 *     type="object",
 *     title="Прогресс фазы в профиле",
 *     @OA\Property(property="has_progress", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Пользователю не назначена фаза")
 * )
 */
class ProfileStatisticsSchemas {}

/**
 * @OA\Schema(
 *     schema="ProfileExerciseInfo",
 *     type="object",
 *     title="Информация об упражнении в профиле",
 *     @OA\Property(property="id", type="integer", example=17),
 *     @OA\Property(property="title", type="string", example="Скручивания на пресс"),
 *     @OA\Property(property="muscle_group", type="string", example="Пресс")
 * )
 */
class ProfileExerciseInfoSchema {}

/**
 * @OA\Schema(
 *     schema="VolumePeriod",
 *     type="object",
 *     title="Период статистики объема",
 *     @OA\Property(property="start", type="string", format="date", example="2026-03-16"),
 *     @OA\Property(property="end", type="string", format="date", example="2026-03-22"),
 *     @OA\Property(property="label", type="string", example="Неделя 4"),
 *     @OA\Property(property="week_number", type="integer", example=4),
 *     @OA\Property(property="week_offset", type="integer", example=0),
 *     @OA\Property(property="can_go_previous", type="boolean", example=false),
 *     @OA\Property(property="can_go_next", type="boolean", example=false)
 * )
 */
class VolumePeriodSchema {}

/**
 * @OA\Schema(
 *     schema="VolumeSummary",
 *     type="object",
 *     title="Сводка по объему",
 *     @OA\Property(property="total_volume", type="number", format="float", example=5225),
 *     @OA\Property(property="workout_count", type="integer", example=3),
 *     @OA\Property(property="average_volume_per_workout", type="number", format="float", example=1741.7)
 * )
 */
class VolumeSummarySchema {}

/**
 * @OA\Schema(
 *     schema="VolumeChartItem",
 *     type="object",
 *     title="Элемент графика объема",
 *     @OA\Property(property="name", type="string", example="Чт"),
 *     @OA\Property(property="total_volume", type="number", format="float", example=5225),
 *     @OA\Property(property="date", type="string", format="date", nullable=true, example="2026-03-19")
 * )
 */
class VolumeChartItemSchema {}

/**
 * @OA\Schema(
 *     schema="VolumeStatistics",
 *     type="object",
 *     title="Статистика объема",
 *     @OA\Property(property="has_data", type="boolean", example=true),
 *     @OA\Property(property="exercise", ref="#/components/schemas/ProfileExerciseInfo"),
 *     @OA\Property(property="average_score", type="number", format="float", example=66.7),
 *     @OA\Property(property="average_score_percent", type="integer", example=66),
 *     @OA\Property(property="average_score_label", type="string", example="Нормально"),
 *     @OA\Property(property="period", ref="#/components/schemas/VolumePeriod"),
 *     @OA\Property(property="summary", ref="#/components/schemas/VolumeSummary"),
 *     @OA\Property(
 *         property="chart",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/VolumeChartItem")
 *     )
 * )
 */
class VolumeStatisticsSchema {}

/**
 * @OA\Schema(
 *     schema="TrendWorkoutInfo",
 *     type="object",
 *     title="Информация о тренировке в тренде",
 *     @OA\Property(property="id", type="integer", example=231),
 *     @OA\Property(property="workout_id", type="integer", example=6),
 *     @OA\Property(property="title", type="string", example="Силовая: Грудь + трицепс"),
 *     @OA\Property(property="completed_at", type="string", format="date", example="2026-03-18"),
 *     @OA\Property(property="completed_at_formatted", type="string", example="18.03.2026 08:32"),
 *     @OA\Property(property="duration_minutes", type="integer", example=0)
 * )
 */
class TrendWorkoutInfoSchema {}

/**
 * @OA\Schema(
 *     schema="TrendChartItem",
 *     type="object",
 *     title="Элемент графика тренда",
 *     @OA\Property(property="exercise_number", type="integer", example=1),
 *     @OA\Property(property="exercise_id", type="integer", example=1),
 *     @OA\Property(property="exercise_name", type="string", example="Жим штанги лежа"),
 *     @OA\Property(property="reaction", type="string", enum={"good", "normal", "bad"}, example="good"),
 *     @OA\Property(property="score", type="integer", example=100),
 *     @OA\Property(property="score_percent", type="integer", example=100),
 *     @OA\Property(property="score_label", type="string", example="Отлично"),
 *     @OA\Property(property="weight_used", type="string", example="60.0"),
 *     @OA\Property(property="sets_completed", type="integer", example=3),
 *     @OA\Property(property="reps_completed", type="integer", example=10),
 *     @OA\Property(property="sets_planned", type="integer", example=3),
 *     @OA\Property(property="reps_planned", type="integer", example=10)
 * )
 */
class TrendChartItemSchema {}

/**
 * @OA\Schema(
 *     schema="AvailableWorkout",
 *     type="object",
 *     title="Доступная тренировка для выбора",
 *     @OA\Property(property="id", type="integer", example=231),
 *     @OA\Property(property="title", type="string", example="Силовая: Грудь + трицепс"),
 *     @OA\Property(property="date", type="string", example="18.03.2026"),
 *     @OA\Property(property="is_current", type="boolean", example=true)
 * )
 */
class AvailableWorkoutSchema {}

/**
 * @OA\Schema(
 *     schema="TrendStatistics",
 *     type="object",
 *     title="Статистика тренда",
 *     @OA\Property(property="has_data", type="boolean", example=true),
 *     @OA\Property(property="workout", ref="#/components/schemas/TrendWorkoutInfo"),
 *     @OA\Property(property="average_score", type="number", format="float", example=100),
 *     @OA\Property(property="average_score_percent", type="integer", example=100),
 *     @OA\Property(property="average_score_label", type="string", example="Отлично"),
 *     @OA\Property(
 *         property="chart",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/TrendChartItem")
 *     ),
 *     @OA\Property(
 *         property="available_workouts",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/AvailableWorkout")
 *     )
 * )
 */
class TrendStatisticsSchema {}

/**
 * @OA\Schema(
 *     schema="FrequencyPeriodInfo",
 *     type="object",
 *     title="Информация о периоде частоты",
 *     @OA\Property(property="type", type="string", enum={"week", "month", "3months", "6months", "year"}, example="month"),
 *     @OA\Property(property="offset", type="integer", example=0),
 *     @OA\Property(property="label", type="string", example="Текущий месяц"),
 *     @OA\Property(property="items_count", type="integer", example=4)
 * )
 */
class FrequencyPeriodInfoSchema {}

/**
 * @OA\Schema(
 *     schema="FrequencySummary",
 *     type="object",
 *     title="Сводка по частоте",
 *     @OA\Property(property="total_workouts", type="integer", example=10),
 *     @OA\Property(property="average_per_week", type="number", format="float", example=2.3),
 *     @OA\Property(property="current_streak", type="integer", example=0),
 *     @OA\Property(property="longest_streak", type="integer", example=1),
 *     @OA\Property(property="weekly_goal", type="integer", example=4)
 * )
 */
class FrequencySummarySchema {}

/**
 * @OA\Schema(
 *     schema="FrequencyChartItem",
 *     type="object",
 *     title="Элемент графика частоты",
 *     @OA\Property(property="week_index", type="integer", example=3),
 *     @OA\Property(property="week_number", type="integer", example=1),
 *     @OA\Property(property="label", type="string", example="Нед 1"),
 *     @OA\Property(property="short_label", type="string", example="1"),
 *     @OA\Property(property="start_date", type="string", format="date", example="2026-02-23"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2026-03-01"),
 *     @OA\Property(property="count", type="integer", example=1),
 *     @OA\Property(property="goal", type="integer", example=4)
 * )
 */
class FrequencyChartItemSchema {}

/**
 * @OA\Schema(
 *     schema="FrequencyStatistics",
 *     type="object",
 *     title="Статистика частоты",
 *     @OA\Property(property="has_data", type="boolean", example=true),
 *     @OA\Property(property="period_info", ref="#/components/schemas/FrequencyPeriodInfo"),
 *     @OA\Property(property="summary", ref="#/components/schemas/FrequencySummary"),
 *     @OA\Property(
 *         property="chart",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/FrequencyChartItem")
 *     )
 * )
 */
class FrequencyStatisticsSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileStatisticsResponse",
 *     type="object",
 *     title="Ответ со всей статистикой профиля",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="current_phase", ref="#/components/schemas/ProfilePhaseProgress"),
 *                 @OA\Property(property="volume", ref="#/components/schemas/VolumeStatistics"),
 *                 @OA\Property(property="trend", ref="#/components/schemas/TrendStatistics"),
 *                 @OA\Property(property="frequency", ref="#/components/schemas/FrequencyStatistics")
 *             )
 *         )
 *     }
 * )
 */
class ProfileStatisticsResponseSchema {}

/**
 * @OA\Schema(
 *     schema="VolumeResponse",
 *     type="object",
 *     title="Ответ со статистикой объема",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/VolumeStatistics"
 *             )
 *         )
 *     }
 * )
 */
class VolumeResponseSchema {}

/**
 * @OA\Schema(
 *     schema="TrendResponse",
 *     type="object",
 *     title="Ответ со статистикой тренда",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/TrendStatistics"
 *             )
 *         )
 *     }
 * )
 */
class TrendResponseSchema {}

/**
 * @OA\Schema(
 *     schema="FrequencyResponse",
 *     type="object",
 *     title="Ответ со статистикой частоты",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/FrequencyStatistics"
 *             )
 *         )
 *     }
 * )
 */
class FrequencyResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileExerciseItem",
 *     type="object",
 *     title="Элемент списка упражнений в профиле",
 *     @OA\Property(property="id", type="integer", example=6),
 *     @OA\Property(property="name", type="string", example="Подтягивания"),
 *     @OA\Property(property="last_used", type="string", format="date", example="2026-03-19"),
 *     @OA\Property(property="last_used_formatted", type="string", example="19.03.2026")
 * )
 */
class ProfileExerciseItemSchema {}

/**
 * @OA\Schema(
 *     schema="ProfileWorkoutItem",
 *     type="object",
 *     title="Элемент списка тренировок в профиле",
 *     @OA\Property(property="id", type="integer", example=231),
 *     @OA\Property(property="workout_id", type="integer", example=6),
 *     @OA\Property(property="title", type="string", example="Силовая: Грудь + трицепс"),
 *     @OA\Property(property="completed_at", type="string", format="date", example="2026-03-18"),
 *     @OA\Property(property="completed_at_formatted", type="string", example="18.03.2026"),
 *     @OA\Property(property="duration_minutes", type="integer", example=0)
 * )
 */
class ProfileWorkoutItemSchema {}
