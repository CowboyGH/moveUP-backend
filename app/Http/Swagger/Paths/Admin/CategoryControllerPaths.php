<?php

namespace App\Http\Swagger\Paths\Admin;

/**
 * @OA\Get(
 *     path="/api/admin/categories",
 *     summary="Получить список всех категорий тестов с фильтрацией",
 *     description="Возвращает список всех категорий с количеством привязанных тестов. Поддерживает поиск, фильтрацию и пагинацию",
 *     operationId="getCategoriesList",
 *     tags={"Admin Categories"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Поиск по названию категории",
 *         required=false,
 *         @OA\Schema(type="string", maxLength=100, example="Бокс")
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Количество элементов на странице (1-100)",
 *         required=false,
 *         @OA\Schema(type="integer", default=15, minimum=1, maximum=100)
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Номер страницы",
 *         required=false,
 *         @OA\Schema(type="integer", default=1, minimum=1)
 *     ),
 *     @OA\Parameter(
 *         name="sort_by",
 *         in="query",
 *         description="Поле для сортировки",
 *         required=false,
 *         @OA\Schema(type="string", enum={"id", "name", "created_at", "updated_at"}, default="created_at")
 *     ),
 *     @OA\Parameter(
 *         name="sort_dir",
 *         in="query",
 *         description="Направление сортировки",
 *         required=false,
 *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
 *     ),
 *     @OA\Parameter(
 *         name="date_from",
 *         in="query",
 *         description="Начальная дата создания (Y-m-d)",
 *         required=false,
 *         @OA\Schema(type="string", format="date", example="2026-01-01")
 *     ),
 *     @OA\Parameter(
 *         name="date_to",
 *         in="query",
 *         description="Конечная дата создания (Y-m-d)",
 *         required=false,
 *         @OA\Schema(type="string", format="date", example="2026-12-31")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
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
 *             ),
 *             @OA\Property(
 *                 property="meta",
 *                 type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=5),
 *                 @OA\Property(property="per_page", type="integer", example=15),
 *                 @OA\Property(property="total", type="integer", example=75),
 *                 @OA\Property(property="from", type="integer", example=1),
 *                 @OA\Property(property="to", type="integer", example=15)
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
 *         description="Ошибка валидации параметров",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
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
