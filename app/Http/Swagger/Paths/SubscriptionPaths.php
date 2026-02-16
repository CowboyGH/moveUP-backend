<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Get(
 *     path="/api/subscriptions",
 *     summary="Получить список всех активных подписок",
 *     tags={"Subscriptions"},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. Список доступных подписок",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="1 month"),
 *                     @OA\Property(property="description", type="string", example="Full access for 1 month"),
 *                     @OA\Property(property="price", type="string", example="9.99"),
 *                     @OA\Property(property="duration_days", type="integer", example=30),
 *                     @OA\Property(property="is_active", type="boolean", example=true),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера"
 *     )
 * )
 */
class SubscriptionPaths {}

/**
 * @OA\Get(
 *     path="/api/subscriptions/{id}",
 *     summary="Получить информацию о конкретной подписке",
 *     tags={"Subscriptions"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID подписки",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. Информация о подписке",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="1 month"),
 *                 @OA\Property(property="description", type="string", example="Full access for 1 month"),
 *                 @OA\Property(property="price", type="string", example="9.99"),
 *                 @OA\Property(property="duration_days", type="integer", example=30),
 *                 @OA\Property(property="is_active", type="boolean", example=true),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Подписка не найдена",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Подписка не найдена")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера"
 *     )
 * )
 */
class SubscriptionShow {}

/**
 * @OA\Get(
 *     path="/api/my-subscriptions",
 *     summary="Получить историю подписок текущего пользователя",
 *     tags={"Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. История подписок пользователя",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="active",
 *                     type="object",
 *                     nullable=true,
 *                     @OA\Property(property="id", type="integer", example=22),
 *                     @OA\Property(property="name", type="string", example="3 month"),
 *                     @OA\Property(property="end_date", type="string", format="date", example="2026-05-16"),
 *                     @OA\Property(property="days_left", type="integer", example=88)
 *                 ),
 *                 @OA\Property(
 *                     property="history",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=21),
 *                         @OA\Property(
 *                             property="subscription",
 *                             type="object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="1 month"),
 *                             @OA\Property(property="price", type="string", example="9.99")
 *                         ),
 *                         @OA\Property(property="start_date", type="string", format="date", example="2026-01-16"),
 *                         @OA\Property(property="end_date", type="string", format="date", example="2026-02-16"),
 *                         @OA\Property(property="is_active", type="boolean", example=false),
 *                         @OA\Property(
 *                             property="status",
 *                             type="string",
 *                             enum={"active", "expired", "cancelled", "inactive"},
 *                             example="expired"
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера"
 *     )
 * )
 */
class MySubscriptions {}
