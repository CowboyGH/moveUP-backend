<?php

namespace App\Http\Swagger\Paths\Admin;

/**
 * @OA\Get(
 *     path="/api/admin/testing-exercises",
 *     summary="Получить список всех тестовых упражнений",
 *     description="Возвращает список всех тестовых упражнений с количеством тестов, в которых они используются",
 *     operationId="getTestingExercisesList",
 *     tags={"Admin Testing Exercises"},
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
 *                     @OA\Property(property="description", type="string", example="Отжимания от пола - максимальное количество за 1 минуту"),
 *                     @OA\Property(property="image", type="string", example="/uploads/exercises/pushups.jpg"),
 *                     @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                     @OA\Property(property="testings_count", type="integer", example=2)
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
 *         @OA\JsonContent(
 *             required={"description", "image"},
 *             @OA\Property(property="description", type="string", example="Отжимания от пола - максимальное количество за 1 минуту", description="Описание упражнения"),
 *             @OA\Property(property="image", type="string", example="/uploads/exercises/pushups.jpg", description="Путь к изображению")
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
 *     description="Возвращает информацию о конкретном тестовом упражнении с тестами, в которых оно используется",
 *     operationId="getTestingExerciseById",
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
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="description", type="string", example="Отжимания от пола - максимальное количество за 1 минуту"),
 *                 @OA\Property(property="image", type="string", example="/uploads/exercises/pushups.jpg"),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2026-02-20T08:20:35.000000Z"),
 *                 @OA\Property(property="testings_count", type="integer", example=2),
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
 *                             @OA\Property(property="testing_exercise_id", type="integer", example=1),
 *                             @OA\Property(property="testing_id", type="integer", example=72),
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
 *         response=422,
 *         description="Нельзя удалить упражнение, которое используется в тестах",
 *         @OA\JsonContent(ref="#/components/schemas/ConflictResponse")
 *     )
 * )
 */
class DeleteTestingExercise {}
