<?php

namespace App\Http\Swagger\Paths\Admin;

/**
 * @OA\Get(
 *     path="/api/admin/overview",
 *     summary="Получить общую статистику",
 *     description="Возвращает общую статистику по выручке и подпискам",
 *     operationId="getStatisticsOverview",
 *     tags={"Admin Statistics"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(ref="#/components/schemas/StatisticsOverviewResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     )
 * )
 */
class StatisticsPaths {}

/**
 * @OA\Get(
 *     path="/api/admin/revenue",
 *     summary="Получить статистику выручки по месяцам",
 *     description="Возвращает выручку по месяцам за указанный год. Значения в тысячах рублей.",
 *     operationId="getRevenueStatistics",
 *     tags={"Admin Statistics"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="year",
 *         in="query",
 *         description="Год для статистики (по умолчанию текущий)",
 *         required=false,
 *         @OA\Schema(type="integer", example=2026)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(ref="#/components/schemas/RevenueResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации параметров",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class RevenueStatisticsPaths {}

/**
 * @OA\Get(
 *     path="/api/admin/subscriptions/count",
 *     summary="Получить статистику количества подписок по месяцам",
 *     description="Возвращает количество купленных подписок по месяцам за указанный год",
 *     operationId="getSubscriptionsCountStatistics",
 *     tags={"Admin Statistics"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="year",
 *         in="query",
 *         description="Год для статистики (по умолчанию текущий)",
 *         required=false,
 *         @OA\Schema(type="integer", example=2026)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(ref="#/components/schemas/SubscriptionsCountResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации параметров",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class SubscriptionsCountStatisticsPaths {}

/**
 * @OA\Get(
 *     path="/api/admin/subscriptions/period",
 *     summary="Получить статистику подписок за период",
 *     description="Возвращает количество подписок по месяцам за указанный период (1, 3, 6 или 12 месяцев)",
 *     operationId="getSubscriptionsByPeriod",
 *     tags={"Admin Statistics"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="period",
 *         in="query",
 *         description="Период в месяцах",
 *         required=false,
 *         @OA\Schema(type="integer", enum={1, 3, 6, 12}, default=1, example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(ref="#/components/schemas/SubscriptionsByPeriodResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации параметров (период должен быть 1, 3, 6 или 12)",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class SubscriptionsByPeriodStatisticsPaths {}
