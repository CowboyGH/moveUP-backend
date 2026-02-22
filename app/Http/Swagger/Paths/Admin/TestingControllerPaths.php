<?php

namespace App\Http\Swagger\Paths\Admin;

/**
 * @OA\Get(
 *     path="/api/admin/testings",
 *     summary="Получить список всех тестов",
 *     description="Возвращает список всех тестов с категориями, упражнениями и количеством результатов",
 *     operationId="getTestingsList",
 *     tags={"Admin Testings"},
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
 *                     @OA\Property(property="id", type="integer", example=72),
 *                     @OA\Property(property="title", type="string", example="Базовая диагностика"),
 *                     @OA\Property(property="description", type="string", example="Тест для определения базового уровня физической подготовки"),
 *                     @OA\Property(property="duration_minutes", type="string", example="15-20 минут"),
 *                     @OA\Property(property="image", type="string", example="/uploads/tests/basic-diagnostic.jpg"),
 *                     @OA\Property(property="is_active", type="boolean", example=true),
 *                     @OA\Property(property="test_results_count", type="integer", example=0),
 *                     @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *                     @OA\Property(
 *                         property="categories",
 *                         type="array",
 *                         @OA\Items(
 *                             type="object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="Бокс и единоборства"),
 *                             @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                             @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                             @OA\Property(
 *                                 property="pivot",
 *                                 type="object",
 *                                 @OA\Property(property="testing_id", type="integer", example=72),
 *                                 @OA\Property(property="category_id", type="integer", example=1),
 *                                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *                                 @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z")
 *                             )
 *                         )
 *                     ),
 *                     @OA\Property(
 *                         property="test_exercises",
 *                         type="array",
 *                         @OA\Items(
 *                             type="object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="description", type="string", example="Отжимания от пола - максимальное количество за 1 минуту"),
 *                             @OA\Property(property="image", type="string", example="/uploads/exercises/pushups.jpg"),
 *                             @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                             @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                             @OA\Property(
 *                                 property="pivot",
 *                                 type="object",
 *                                 @OA\Property(property="testing_id", type="integer", example=72),
 *                                 @OA\Property(property="testing_exercise_id", type="integer", example=1),
 *                                 @OA\Property(property="order_number", type="integer", example=0),
 *                                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *                                 @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z")
 *                             )
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
 *     )
 * )
 */
class TestingControllerPaths {}

/**
 * @OA\Post(
 *     path="/api/admin/testings",
 *     summary="Создать новый тест",
 *     description="Создает новый тест с категориями и упражнениями",
 *     operationId="createTesting",
 *     tags={"Admin Testings"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title", "description", "duration_minutes", "image"},
 *             @OA\Property(property="title", type="string", example="Базовая диагностика", description="Название теста"),
 *             @OA\Property(property="description", type="string", example="Тест для определения базового уровня физической подготовки", description="Описание теста"),
 *             @OA\Property(property="duration_minutes", type="string", example="15-20 минут", description="Длительность теста"),
 *             @OA\Property(property="image", type="string", example="/uploads/tests/basic-diagnostic.jpg", description="Путь к изображению"),
 *             @OA\Property(property="is_active", type="boolean", example=true, description="Активен ли тест"),
 *             @OA\Property(
 *                 property="category_ids",
 *                 type="array",
 *                 description="ID категорий теста",
 *                 @OA\Items(type="integer", example=1)
 *             ),
 *             @OA\Property(
 *                 property="exercise_ids",
 *                 type="array",
 *                 description="ID упражнений теста (порядок определяется индексом в массиве)",
 *                 @OA\Items(type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Тест успешно создан",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Тест успешно создан"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=75),
 *                 @OA\Property(property="title", type="string", example="Базовая диагностика 2"),
 *                 @OA\Property(property="description", type="string", example="Тест для определения базового уровня физической подготовки"),
 *                 @OA\Property(property="duration_minutes", type="string", example="15-20 минут"),
 *                 @OA\Property(property="image", type="string", example="/uploads/tests/basic-diagnostic.jpg"),
 *                 @OA\Property(property="is_active", type="boolean", example=true),
 *                 @OA\Property(
 *                     property="categories",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Бокс и единоборства"),
 *                         @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                         @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                         @OA\Property(
 *                             property="pivot",
 *                             type="object",
 *                             @OA\Property(property="testing_id", type="integer", example=75),
 *                             @OA\Property(property="category_id", type="integer", example=1),
 *                             @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-22T06:29:58.000000Z"),
 *                             @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-22T06:29:58.000000Z")
 *                         )
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="exercises",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=24),
 *                         @OA\Property(property="description", type="string", example="Отжимания от пола - обновленное описание"),
 *                         @OA\Property(property="image", type="string", example="/uploads/exercises/pushups-updkjfhjkfhsdfjkhsdfjkdshfkjdshfdsjklfhafbsfdmnsbfjhsfhgfhdasjddajhfgsdjkadhjadhaskjdhajkdhafjguwrhdfghkfdsghefrwhousbdshkdfhjfdhjhbdbhbyrwiybdsbdsjhbjkdfsjkbhdfsjkbfsdhjkbfsdsojweyated.jpg"),
 *                         @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-22T05:16:17.000000Z"),
 *                         @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-22T05:19:44.000000Z"),
 *                         @OA\Property(
 *                             property="pivot",
 *                             type="object",
 *                             @OA\Property(property="testing_id", type="integer", example=75),
 *                             @OA\Property(property="testing_exercise_id", type="integer", example=24),
 *                             @OA\Property(property="order_number", type="integer", example=0),
 *                             @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-22T06:29:58.000000Z"),
 *                             @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-22T06:29:58.000000Z")
 *                         )
 *                     )
 *                 ),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-22T06:29:58.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-22T06:29:58.000000Z")
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
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Ошибка сервера при создании теста",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class CreateTesting {}

/**
 * @OA\Get(
 *     path="/api/admin/testings/{id}",
 *     summary="Получить тест по ID",
 *     description="Возвращает информацию о конкретном тесте с категориями, упражнениями и результатами пользователей",
 *     operationId="getTestingById",
 *     tags={"Admin Testings"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID теста",
 *         @OA\Schema(type="integer", example=72)
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
 *                 @OA\Property(property="id", type="integer", example=72),
 *                 @OA\Property(property="title", type="string", example="Базовая диагностика"),
 *                 @OA\Property(property="description", type="string", example="Тест для определения базового уровня физической подготовки"),
 *                 @OA\Property(property="duration_minutes", type="string", example="15-20 минут"),
 *                 @OA\Property(property="image", type="string", example="/uploads/tests/basic-diagnostic.jpg"),
 *                 @OA\Property(property="is_active", type="boolean", example=true),
 *                 @OA\Property(property="test_results_count", type="integer", example=0),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *                 @OA\Property(
 *                     property="categories",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Бокс и единоборства"),
 *                         @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                         @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="exercises",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="description", type="string", example="Отжимания от пола - максимальное количество за 1 минуту"),
 *                         @OA\Property(property="image", type="string", example="/uploads/exercises/pushups.jpg"),
 *                         @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                         @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                         @OA\Property(
 *                             property="pivot",
 *                             type="object",
 *                             @OA\Property(property="testing_id", type="integer", example=72),
 *                             @OA\Property(property="testing_exercise_id", type="integer", example=1),
 *                             @OA\Property(property="order_number", type="integer", example=0),
 *                             @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *                             @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z")
 *                         )
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="test_results",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="user_id", type="integer", example=5),
 *                         @OA\Property(property="testing_id", type="integer", example=72),
 *                         @OA\Property(property="exercise_id", type="integer", example=1),
 *                         @OA\Property(property="result_value", type="integer", example=25),
 *                         @OA\Property(property="pulse", type="integer", example=120, nullable=true),
 *                         @OA\Property(property="test_date", type="string", format="date", example="2026-02-20"),
 *                         @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *                         @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z")
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
 *         description="Тест не найден",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     )
 * )
 */
class GetTestingById {}

/**
 * @OA\Put(
 *     path="/api/admin/testings/{id}",
 *     summary="Обновить тест",
 *     description="Обновляет существующий тест, категории и упражнения",
 *     operationId="updateTesting",
 *     tags={"Admin Testings"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID теста",
 *         @OA\Schema(type="integer", example=72)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string", example="Базовая диагностика (обновлено)", description="Название теста"),
 *             @OA\Property(property="description", type="string", example="Обновленное описание теста", description="Описание теста"),
 *             @OA\Property(property="duration_minutes", type="string", example="20-25 минут", description="Длительность теста"),
 *             @OA\Property(property="image", type="string", example="/uploads/tests/basic-diagnostic-updated.jpg", description="Путь к изображению"),
 *             @OA\Property(property="is_active", type="boolean", example=false, description="Активен ли тест"),
 *             @OA\Property(
 *                 property="category_ids",
 *                 type="array",
 *                 description="ID категорий теста",
 *                 @OA\Items(type="integer", example=1)
 *             ),
 *             @OA\Property(
 *                 property="exercise_ids",
 *                 type="array",
 *                 description="ID упражнений теста (порядок определяется индексом в массиве)",
 *                 @OA\Items(type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Тест успешно обновлен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Тест успешно обновлен"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=72),
 *                 @OA\Property(property="title", type="string", example="Базовая диагностика (обновлено)"),
 *                 @OA\Property(property="description", type="string", example="Обновленное описание теста"),
 *                 @OA\Property(property="duration_minutes", type="string", example="20-25 минут"),
 *                 @OA\Property(property="image", type="string", example="/uploads/tests/basic-diagnostic-updated.jpg"),
 *                 @OA\Property(property="is_active", type="boolean", example=false),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-22T10:00:00.000000Z"),
 *                 @OA\Property(
 *                     property="categories",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Бокс и единоборства"),
 *                         @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                         @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="exercises",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="description", type="string", example="Отжимания от пола - максимальное количество за 1 минуту"),
 *                         @OA\Property(property="image", type="string", example="/uploads/exercises/pushups.jpg"),
 *                         @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                         @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                         @OA\Property(
 *                             property="pivot",
 *                             type="object",
 *                             @OA\Property(property="testing_id", type="integer", example=72),
 *                             @OA\Property(property="testing_exercise_id", type="integer", example=1),
 *                             @OA\Property(property="order_number", type="integer", example=0),
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
 *         description="Тест не найден",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Ошибка сервера при обновлении теста",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class UpdateTesting {}

/**
 * @OA\Delete(
 *     path="/api/admin/testings/{id}",
 *     summary="Удалить тест",
 *     description="Удаляет тест и все связанные с ним данные (категории и упражнения отвязываются)",
 *     operationId="deleteTesting",
 *     tags={"Admin Testings"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID теста",
 *         @OA\Schema(type="integer", example=72)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Тест успешно удален",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Тест успешно удален")
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
 *         description="Тест не найден",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     )
 * )
 */
class DeleteTesting {}

/**
 * @OA\Patch(
 *     path="/api/admin/testings/{id}/toggle-active",
 *     summary="Переключить активность теста",
 *     description="Переключает статус is_active теста на противоположный",
 *     operationId="toggleTestingActive",
 *     tags={"Admin Testings"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID теста",
 *         @OA\Schema(type="integer", example=72)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Тест успешно активирован/деактивирован",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Тест успешно активирован"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=72),
 *                 @OA\Property(property="title", type="string", example="Базовая диагностика"),
 *                 @OA\Property(property="description", type="string", example="Тест для определения базового уровня физической подготовки"),
 *                 @OA\Property(property="duration_minutes", type="string", example="15-20 минут"),
 *                 @OA\Property(property="image", type="string", example="/uploads/tests/basic-diagnostic.jpg"),
 *                 @OA\Property(property="is_active", type="boolean", example=false),
 *                 @OA\Property(property="test_results_count", type="integer", example=0),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:37:48.000000Z"),
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
 *         description="Тест не найден",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     )
 * )
 */
class ToggleTestingActive {}
