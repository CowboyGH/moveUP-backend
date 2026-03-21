<?php

namespace App\Http\Swagger\Paths\Admin;

/**
 * @OA\Get(
 *     path="/api/admin/subscriptions",
 *     summary="Получить список всех подписок",
 *     description="Возвращает список всех подписок. Поддерживает поиск и пагинацию",
 *     operationId="getSubscriptionsList",
 *     tags={"Admin Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Поиск по названию и описанию",
 *         required=false,
 *         @OA\Schema(type="string", maxLength=100, example="premium")
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Количество элементов на странице (1-100)",
 *         required=false,
 *         @OA\Schema(type="integer", default=10, minimum=1, maximum=100)
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Номер страницы",
 *         required=false,
 *         @OA\Schema(type="integer", default=1, minimum=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="1 month"),
 *                     @OA\Property(property="description", type="string", example="Full access for 1 month"),
 *                     @OA\Property(property="image", type="string", example="http://localhost/storage/subscriptions/abc.jpg"),
 *                     @OA\Property(property="price", type="string", example="9.99"),
 *                     @OA\Property(property="duration_days", type="integer", example=30),
 *                     @OA\Property(property="is_active", type="boolean", example=true),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="meta",
 *                 type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=5),
 *                 @OA\Property(property="per_page", type="integer", example=10),
 *                 @OA\Property(property="total", type="integer", example=50),
 *                 @OA\Property(property="from", type="integer", example=1),
 *                 @OA\Property(property="to", type="integer", example=10)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
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
class SubscriptionPaths {}

/**
 * @OA\Post(
 *     path="/api/admin/subscriptions",
 *     summary="Создать новую подписку",
 *     tags={"Admin Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"name","description","price","duration_days"},
 *                 @OA\Property(property="name", type="string", example="Premium 1 month"),
 *                 @OA\Property(property="description", type="string", example="Full access to all premium features"),
 *                 @OA\Property(property="price", type="number", format="float", example=9.99),
 *                 @OA\Property(property="duration_days", type="integer", example=30, enum={30,90,180,365}),
 *                 @OA\Property(property="is_active", type="boolean", example=true),
 *                 @OA\Property(property="image", type="string", format="binary", description="Изображение (jpg, png, gif, до 2MB)")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Подписка создана",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Подписка успешно создана"),
 *             @OA\Property(
 *                 property="data",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Premium 1 month"),
 *                 @OA\Property(property="description", type="string", example="Full access to all premium features"),
 *                 @OA\Property(property="image", type="string", example="http://localhost/storage/subscriptions/abc.jpg"),
 *                 @OA\Property(property="price", type="string", example="9.99"),
 *                 @OA\Property(property="duration_days", type="integer", example=30),
 *                 @OA\Property(property="is_active", type="boolean", example=true),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован. Возможные причины: истекший токен, невалидный токен или сессия завершена из-за неактивности",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
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
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="1 month"),
 *                 @OA\Property(property="description", type="string", example="Full access for 1 month"),
 *                 @OA\Property(property="image", type="string", example="http://localhost/storage/subscriptions/abc.jpg"),
 *                 @OA\Property(property="price", type="string", example="9.99"),
 *                 @OA\Property(property="duration_days", type="integer", example=30),
 *                 @OA\Property(property="is_active", type="boolean", example=true),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован. Возможные причины: истекший токен, невалидный токен или сессия завершена из-за неактивности",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Подписка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     )
 * )
 */
class SubscriptionShow {}

/**
 * @OA\Put(
 *     path="/api/admin/subscriptions/{id}",
 *     summary="Обновить данные подписки (без изображения)",
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
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Подписка успешно обновлена"),
 *             @OA\Property(
 *                 property="data",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Updated name"),
 *                 @OA\Property(property="description", type="string", example="Updated description"),
 *                 @OA\Property(property="image", type="string", example="http://localhost/storage/subscriptions/abc.jpg"),
 *                 @OA\Property(property="price", type="string", example="19.99"),
 *                 @OA\Property(property="duration_days", type="integer", example=90),
 *                 @OA\Property(property="is_active", type="boolean", example=false),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован. Возможные причины: истекший токен, невалидный токен или сессия завершена из-за неактивности",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Подписка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class SubscriptionUpdate {}

/**
 * @OA\Post(
 *     path="/api/admin/subscriptions/{id}/image",
 *     summary="Загрузить/обновить изображение подписки",
 *     tags={"Admin Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"image"},
 *                 @OA\Property(property="image", type="string", format="binary", description="Изображение (jpg, png, gif, до 2MB)")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Изображение обновлено",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Изображение подписки обновлено"),
 *             @OA\Property(
 *                 property="data",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="1 month"),
 *                 @OA\Property(property="description", type="string", example="Full access for 1 month"),
 *                 @OA\Property(property="image", type="string", example="http://localhost/storage/subscriptions/new.jpg"),
 *                 @OA\Property(property="price", type="string", example="9.99"),
 *                 @OA\Property(property="duration_days", type="integer", example=30),
 *                 @OA\Property(property="is_active", type="boolean", example=true),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован. Возможные причины: истекший токен, невалидный токен или сессия завершена из-за неактивности",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Подписка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации (файл не загружен или не соответствует требованиям)",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class SubscriptionUpdateImage {}

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
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Подписка успешно удалена")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован. Возможные причины: истекший токен, невалидный токен или сессия завершена из-за неактивности",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Подписка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Нельзя удалить используемую подписку",
 *         @OA\JsonContent(ref="#/components/schemas/ConflictResponse")
 *     )
 * )
 */
class SubscriptionDestroy {}
