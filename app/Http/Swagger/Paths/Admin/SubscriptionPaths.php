<?php

namespace App\Http\Swagger\Paths\Admin;

/**
 * @OA\Get(
 *     path="/api/admin/subscriptions",
 *     summary="Получить список всех подписок",
 *     tags={"Admin Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
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
 *     @OA\Response(response=401, description="Не авторизован"),
 *     @OA\Response(response=403, description="Доступ запрещен")
 * )
 */
class SubscriptionPaths {}

/**
 * @OA\Post(
 *     path="/api/admin/subscriptions",
 *     summary="Создать новую подписку",
 *     tags={"Admin Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","description","price","duration_days"},
 *             @OA\Property(property="name", type="string", example="Premium 1 month"),
 *             @OA\Property(property="description", type="string", example="Full access to all premium features"),
 *             @OA\Property(property="price", type="number", format="float", example=9.99),
 *             @OA\Property(property="duration_days", type="integer", example=30, enum={30,90,180,365}),
 *             @OA\Property(property="is_active", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Подписка создана",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Подписка успешно создана"),
 *             @OA\Property(
 *                 property="data",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="description", type="string"),
 *                 @OA\Property(property="price", type="string"),
 *                 @OA\Property(property="duration_days", type="integer"),
 *                 @OA\Property(property="is_active", type="boolean"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Не авторизован"),
 *     @OA\Response(response=403, description="Доступ запрещен"),
 *     @OA\Response(response=422, description="Ошибка валидации")
 * )
 */
class SubscriptionStore {}

/**
 * @OA\Get(
 *     path="/api/admin/subscriptions/{id}",
 *     summary="Получить информацию о подписке",
 *     tags={"Admin Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="description", type="string"),
 *                 @OA\Property(property="price", type="string"),
 *                 @OA\Property(property="duration_days", type="integer"),
 *                 @OA\Property(property="is_active", type="boolean"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Не авторизован"),
 *     @OA\Response(response=403, description="Доступ запрещен"),
 *     @OA\Response(response=404, description="Подписка не найдена")
 * )
 */
class SubscriptionShow {}

/**
 * @OA\Put(
 *     path="/api/admin/subscriptions/{id}",
 *     summary="Обновить подписку",
 *     tags={"Admin Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Updated name"),
 *             @OA\Property(property="description", type="string", example="Updated description"),
 *             @OA\Property(property="price", type="number", format="float", example=19.99),
 *             @OA\Property(property="duration_days", type="integer", example=90, enum={30,90,180,365}),
 *             @OA\Property(property="is_active", type="boolean", example=false)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Подписка обновлена",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Подписка успешно обновлена"),
 *             @OA\Property(
 *                 property="data",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="description", type="string"),
 *                 @OA\Property(property="price", type="string"),
 *                 @OA\Property(property="duration_days", type="integer"),
 *                 @OA\Property(property="is_active", type="boolean"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Не авторизован"),
 *     @OA\Response(response=403, description="Доступ запрещен"),
 *     @OA\Response(response=404, description="Подписка не найдена"),
 *     @OA\Response(response=422, description="Ошибка валидации")
 * )
 */
class SubscriptionUpdate {}

/**
 * @OA\Delete(
 *     path="/api/admin/subscriptions/{id}",
 *     summary="Удалить подписку",
 *     tags={"Admin Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Подписка удалена",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Подписка успешно удалена")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Не авторизован"),
 *     @OA\Response(response=403, description="Доступ запрещен"),
 *     @OA\Response(response=404, description="Подписка не найдена"),
 *     @OA\Response(response=422, description="Нельзя удалить используемую подписку")
 * )
 */
class SubscriptionDestroy {}
