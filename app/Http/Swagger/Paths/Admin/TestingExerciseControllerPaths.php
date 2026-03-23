<?php

namespace App\Http\Swagger\Paths\Admin;

/**
 * @OA\Get(
 *     path="/api/admin/testing-exercises",
 *     summary="Получить список всех тестовых упражнений",
 *     description="Возвращает список всех тестовых упражнений с количеством тестов, в которых они используются. Поддерживает поиск и пагинацию",
 *     operationId="getTestingExercisesList",
 *     tags={"Admin Testing Exercises"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Поиск по описанию упражнения",
 *         required=false,
 *         @OA\Schema(type="string", maxLength=100, example="отжимания")
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
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="exercise_id", type="integer", example=1),
 *                     @OA\Property(property="description", type="string", example="Отжимания от пола - максимальное количество за 1 минуту"),
 *                     @OA\Property(property="image", type="string", example="/uploads/exercises/pushups.jpg"),
 *                     @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                     @OA\Property(property="testings_count", type="integer", example=2)
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
class TestingExerciseControllerPaths {}

/**
 * @OA\Post(
 *     path="/api/admin/testing-exercises",
 *     summary="Создать новое тестовое упражнение",
 *     description="Создает новое тестовое упражнение",
 *     operationId="createTestingExercise",
 *     tags={"Admin Testing Exercises"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"exercise_id", "description"},
 *                 @OA\Property(property="exercise_id", type="integer", example=5, description="ID упражнения из таблицы exercises (основной каталог упражнений)"),
 *                 @OA\Property(property="description", type="string", example="Отжимания от пола - максимальное количество за 1 минуту", description="Описание упражнения"),
 *                 @OA\Property(property="image", type="string", format="binary", description="Изображение упражнения (jpg, png, gif, до 5MB)")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Тестовое упражнение успешно создано",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Тестовое упражнение успешно создано"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="exercise_id", type="integer", example=5),
 *                 @OA\Property(property="description", type="string", example="Отжимания от пола - максимальное количество за 1 минуту"),
 *                 @OA\Property(property="image", type="string", example="/uploads/exercises/pushups.jpg"),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-22T10:00:00.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-22T10:00:00.000000Z"),
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
class CreateTestingExercise {}

/**
 * @OA\Get(
 *     path="/api/admin/testing-exercises/{id}",
 *     summary="Получить тестовое упражнение по ID",
 *     description="Возвращает информацию о конкретном тестовом упражнении и тестах, в которых оно используется",
 *     operationId="getTestingExerciseById",
 *     tags={"Admin Testing Exercises"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID тестового упражнения",
 *         @OA\Schema(type="integer", example=4)
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
 *                 @OA\Property(property="id", type="integer", example=4),
 *                 @OA\Property(property="exercise_id", type="integer", example=9),
 *                 @OA\Property(property="description", type="string", example="Определение максимального веса в жиме лежа. Выполняются подходы с постепенным увеличением веса до достижения одноповторного максимума."),
 *                 @OA\Property(property="image", type="string", example="testing-exercises/bench-press-1rm.jpg"),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-03-20T08:57:06.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2026-03-20T08:57:06.000000Z"),
 *                 @OA\Property(
 *                     property="testings",
 *                     type="array",
 *                     description="Список тестов, в которых используется это упражнение",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=2),
 *                         @OA\Property(property="title", type="string", example="Гарвардский степ-тест"),
 *                         @OA\Property(property="description", type="string", example="Оценка восстановления сердечно-сосудистой системы после нагрузки."),
 *                         @OA\Property(property="duration_minutes", type="string", example="15"),
 *                         @OA\Property(property="image", type="string", example="tests/harvard-step.jpg"),
 *                         @OA\Property(property="is_active", type="boolean", example=true),
 *                         @OA\Property(property="created_at", type="string", format="datetime", example="2026-03-20T08:57:06.000000Z"),
 *                         @OA\Property(property="updated_at", type="string", format="datetime", example="2026-03-20T08:57:06.000000Z"),
 *                         @OA\Property(
 *                             property="pivot",
 *                             type="object",
 *                             @OA\Property(property="testing_exercise_id", type="integer", example=4),
 *                             @OA\Property(property="testing_id", type="integer", example=2),
 *                             @OA\Property(property="order_number", type="integer", example=1),
 *                             @OA\Property(property="created_at", type="string", format="datetime", example="2026-03-20T08:57:06.000000Z"),
 *                             @OA\Property(property="updated_at", type="string", format="datetime", example="2026-03-20T08:57:06.000000Z")
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
 *         description="Тестовое упражнение не найдено",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     )
 * )
 */
class GetTestingExerciseById {}

/**
 * @OA\Put(
 *     path="/api/admin/testing-exercises/{id}",
 *     summary="Обновить тестовое упражнение",
 *     description="Обновляет существующее тестовое упражнение",
 *     operationId="updateTestingExercise",
 *     tags={"Admin Testing Exercises"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID тестового упражнения",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="exercise_id", type="integer", example=5, description="ID упражнения из таблицы exercises (основной каталог упражнений)"),
 *             @OA\Property(property="description", type="string", example="Отжимания от пола - обновленное описание", description="Описание упражнения"),
 *             @OA\Property(property="image", type="string", example="/uploads/exercises/pushups-updated.jpg", description="Путь к изображению")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Тестовое упражнение успешно обновлено",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Тестовое упражнение успешно обновлено"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="exercise_id", type="integer", example=5),
 *                 @OA\Property(property="description", type="string", example="Отжимания от пола - обновленное описание"),
 *                 @OA\Property(property="image", type="string", example="/uploads/exercises/pushups-updated.jpg"),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-22T10:00:00.000000Z"),
 *                 @OA\Property(property="testings_count", type="integer", example=2)
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
 *         description="Тестовое упражнение не найдено",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class UpdateTestingExercise {}

/**
 * @OA\Delete(
 *     path="/api/admin/testing-exercises/{id}",
 *     summary="Удалить тестовое упражнение",
 *     description="Удаляет тестовое упражнение, если оно не используется в тестах",
 *     operationId="deleteTestingExercise",
 *     tags={"Admin Testing Exercises"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID тестового упражнения",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Тестовое упражнение успешно удалено",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Тестовое упражнение успешно удалено")
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
 *         description="Тестовое упражнение не найдено",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Нельзя удалить упражнение, которое используется в тестах",
 *         @OA\JsonContent(ref="#/components/schemas/ConflictResponse")
 *     )
 * )
 */
class DeleteTestingExercise {}

/**
 * @OA\Post(
 *     path="/api/admin/testing-exercises/{id}/image",
 *     summary="Загрузить/обновить изображение тестового упражнения",
 *     tags={"Admin Testing Exercises"},
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
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="binary",
 *                     description="Изображение (jpg, png, gif, до 5MB)"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Изображение обновлено",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Изображение тестового упражнения обновлено"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="exercise_id", type="integer", example=5),
 *                 @OA\Property(property="description", type="string", example="Отжимания от пола - максимальное количество за 1 минуту"),
 *                 @OA\Property(property="image", type="string", example="http://localhost/storage/testing-exercises/abc.jpg"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *                 @OA\Property(
 *                     property="exercise",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=5),
 *                     @OA\Property(property="title", type="string", example="Отжимания")
 *                 )
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
 *         response=404,
 *         description="Тестовое упражнение не найдено",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации (файл не загружен или не соответствует требованиям)",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class TestingExerciseUpdateImage {}
