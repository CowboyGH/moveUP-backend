<?php

namespace App\Http\Swagger\Paths\Admin;

/**
 * @OA\Get(
 *     path="/api/admin/categories",
 *     summary="Получить список всех категорий тестов",
 *     description="Возвращает список всех категорий с количеством привязанных тестов",
 *     operationId="getCategoriesList",
 *     tags={"Admin Categories"},
 *     security={{"bearerAuth":{}}},
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
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Бокс и единоборства"),
 *                     @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                     @OA\Property(property="testings_count", type="integer", example=3)
 *                 )
 *             )
 *         )
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
class CategoryControllerPaths {}

/**
 * @OA\Post(
 *     path="/api/admin/categories",
 *     summary="Создать новую категорию тестов",
 *     description="Создает новую категорию для тестов",
 *     operationId="createCategory",
 *     tags={"Admin Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Бокс и единоборства", description="Название категории")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Категория успешно создана",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Категория успешно создана"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Бокс и единоборства"),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                 @OA\Property(property="testings_count", type="integer", example=0)
 *             )
 *         )
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
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class CreateCategory {}

/**
 * @OA\Get(
 *     path="/api/admin/categories/{id}",
 *     summary="Получить категорию по ID",
 *     description="Возвращает информацию о конкретной категории с привязанными тестами",
 *     operationId="getCategoryById",
 *     tags={"Admin Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID категории",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Бокс и единоборства"),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                 @OA\Property(property="testings_count", type="integer", example=3),
 *                 @OA\Property(
 *                     property="testings",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=72),
 *                         @OA\Property(property="title", type="string", example="Базовая диагностика"),
 *                         @OA\Property(property="description", type="string", example="Тест для определения базового уровня физической подготовки"),
 *                         @OA\Property(property="duration_minutes", type="string", example="15-20 минут"),
 *                         @OA\Property(property="image", type="string", example="/uploads/tests/basic-diagnostic.jpg"),
 *                         @OA\Property(property="is_active", type="boolean", example=true),
 *                         @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *                         @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *                         @OA\Property(
 *                             property="pivot",
 *                             type="object",
 *                             @OA\Property(property="category_id", type="integer", example=1),
 *                             @OA\Property(property="testing_id", type="integer", example=72),
 *                             @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *                             @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z")
 *                         )
 *                     )
 *                 )
 *             )
 *         )
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
 *         response=404,
 *         description="Категория не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     )
 * )
 */
class GetCategoryById {}

/**
 * @OA\Put(
 *     path="/api/admin/categories/{id}",
 *     summary="Обновить категорию",
 *     description="Обновляет существующую категорию",
 *     operationId="updateCategory",
 *     tags={"Admin Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID категории",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Бокс и единоборства (обновлено)", description="Название категории")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Категория успешно обновлена",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Категория успешно обновлена"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Бокс и единоборства (обновлено)"),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-22T10:00:00.000000Z")
 *             )
 *         )
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
 *         response=404,
 *         description="Категория не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class UpdateCategory {}

/**
 * @OA\Delete(
 *     path="/api/admin/categories/{id}",
 *     summary="Удалить категорию",
 *     description="Удаляет категорию, если к ней не привязаны тесты",
 *     operationId="deleteCategory",
 *     tags={"Admin Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID категории",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Категория успешно удалена",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Категория успешно удалена")
 *         )
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
 *         response=404,
 *         description="Категория не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Нельзя удалить категорию, к которой привязаны тесты",
 *         @OA\JsonContent(ref="#/components/schemas/ConflictResponse")
 *     )
 * )
 */
class DeleteCategory {}
