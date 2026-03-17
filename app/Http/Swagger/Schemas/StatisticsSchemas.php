<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="StatisticsOverview",
 *     type="object",
 *     title="Общая статистика",
 *     @OA\Property(property="total_revenue", type="number", format="float", example=125.5, description="Общая выручка в тысячах рублей"),
 *     @OA\Property(property="total_subscriptions", type="integer", example=234, description="Всего подписок"),
 *     @OA\Property(property="active_subscriptions", type="integer", example=89, description="Активных подписок"),
 *     @OA\Property(property="current_month_revenue", type="number", format="float", example=25.3, description="Выручка за текущий месяц в тысячах рублей"),
 *     @OA\Property(property="revenue_growth", type="number", format="float", example=12.5, description="Рост выручки относительно прошлого месяца в процентах")
 * )
 */
class StatisticsSchemas {}

/**
 * @OA\Schema(
 *     schema="StatisticsOverviewResponse",
 *     type="object",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/StatisticsOverview"
 *             )
 *         )
 *     }
 * )
 */
class StatisticsOverviewResponseSchema {}

/**
 * @OA\Schema(
 *     schema="MonthlyStatItem",
 *     type="object",
 *     title="Элемент месячной статистики",
 *     @OA\Property(property="month", type="integer", example=3, description="Номер месяца (1-12)"),
 *     @OA\Property(property="month_name", type="string", example="мар", description="Название месяца"),
 *     @OA\Property(property="value", type="number", format="float", example=25.5, description="Значение (выручка в тыс. руб или количество подписок)")
 * )
 */
class MonthlyStatItemSchema {}

/**
 * @OA\Schema(
 *     schema="RevenueData",
 *     type="object",
 *     title="Данные по выручке",
 *     @OA\Property(property="year", type="integer", example=2026, description="Год"),
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/MonthlyStatItem")
 *     )
 * )
 */
class RevenueDataSchema {}

/**
 * @OA\Schema(
 *     schema="RevenueResponse",
 *     type="object",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/RevenueData"
 *             )
 *         )
 *     }
 * )
 */
class RevenueResponseSchema {}

/**
 * @OA\Schema(
 *     schema="SubscriptionsCountData",
 *     type="object",
 *     title="Данные по количеству подписок",
 *     @OA\Property(property="year", type="integer", example=2026, description="Год"),
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/MonthlyStatItem")
 *     )
 * )
 */
class SubscriptionsCountDataSchema {}

/**
 * @OA\Schema(
 *     schema="SubscriptionsCountResponse",
 *     type="object",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/SubscriptionsCountData"
 *             )
 *         )
 *     }
 * )
 */
class SubscriptionsCountResponseSchema {}

/**
 * @OA\Schema(
 *     schema="PeriodStatItem",
 *     type="object",
 *     title="Элемент статистики за период",
 *     @OA\Property(property="month", type="integer", example=3, description="Номер месяца (1-12)"),
 *     @OA\Property(property="month_name", type="string", example="мар", description="Название месяца"),
 *     @OA\Property(property="year", type="integer", example=2026, description="Год"),
 *     @OA\Property(property="label", type="string", example="мар 2026", description="Подпись для графика"),
 *     @OA\Property(property="value", type="integer", example=25, description="Количество подписок")
 * )
 */
class PeriodStatItemSchema {}

/**
 * @OA\Schema(
 *     schema="SubscriptionsByPeriodData",
 *     type="object",
 *     title="Данные по подпискам за период",
 *     @OA\Property(property="period", type="integer", example=12, description="Период в месяцах (1, 3, 6, 12)"),
 *     @OA\Property(property="start_date", type="string", format="date", example="2025-03-17", description="Начальная дата периода"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2026-03-17", description="Конечная дата периода"),
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/PeriodStatItem")
 *     )
 * )
 */
class SubscriptionsByPeriodDataSchema {}

/**
 * @OA\Schema(
 *     schema="SubscriptionsByPeriodResponse",
 *     type="object",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/SubscriptionsByPeriodData"
 *             )
 *         )
 *     }
 * )
 */
class SubscriptionsByPeriodResponseSchema {}
