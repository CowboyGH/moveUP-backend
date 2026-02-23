<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Get(
 *     path="/api/testings",
 *     summary="Получить список всех активных тестов",
 *     tags={"Testings"},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. Список доступных тестов",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="title", type="string", example="Функциональный тест"),
 *                     @OA\Property(property="description", type="string", example="Оценка функциональной подготовки"),
 *                     @OA\Property(property="duration_minutes", type="string", example="30 минут"),
 *                     @OA\Property(property="image", type="string", example="test.jpg"),
 *                     @OA\Property(
 *                         property="categories",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="Сила")
 *                         )
 *                     ),
 *                     @OA\Property(property="exercises_count", type="integer", example=5)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class TestingPaths {}

/**
 * @OA\Get(
 *     path="/api/testings/{id}",
 *     summary="Получить информацию о конкретном тесте",
 *     tags={"Testings"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID теста",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. Информация о тесте",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Функциональный тест"),
 *                 @OA\Property(property="description", type="string", example="Оценка функциональной подготовки"),
 *                 @OA\Property(property="duration_minutes", type="integer", example=30),
 *                 @OA\Property(property="image", type="string", example="test.jpg"),
 *                 @OA\Property(
 *                     property="categories",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Сила")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="exercises",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="description", type="string", example="Приседания"),
 *                         @OA\Property(property="image", type="string", example="exercise.jpg"),
 *                         @OA\Property(property="order_number", type="integer", example=1)
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Тест не найден",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class TestingShow {}

/**
 * @OA\Get(
 *     path="/api/my-test-history",
 *     summary="Получить историю пройденных тестов текущего пользователя",
 *     tags={"Testings"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. История тестов пользователя",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="statistics",
 *                     type="object",
 *                     @OA\Property(property="total_tests_completed", type="integer", example=10),
 *                     @OA\Property(property="unique_tests_completed", type="integer", example=5),
 *                     @OA\Property(property="last_test_date", type="string", format="date-time", example="2024-01-15T14:30:00Z")
 *                 ),
 *                 @OA\Property(
 *                     property="history",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="testing_id", type="integer", example=1),
 *                         @OA\Property(property="testing_title", type="string", example="Функциональный тест"),
 *                         @OA\Property(property="last_completed_at", type="string", format="date-time", example="2024-01-15T14:30:00Z"),
 *                         @OA\Property(property="total_attempts", type="integer", example=3),
 *                         @OA\Property(
 *                             property="exercises_results",
 *                             type="array",
 *                             @OA\Items(
 *                                 @OA\Property(property="exercise_id", type="integer", example=1),
 *                                 @OA\Property(property="exercise_description", type="string", example="Приседания"),
 *                                 @OA\Property(property="result_value", type="integer", example=4),
 *                                 @OA\Property(property="pulse", type="integer", example=120),
 *                                 @OA\Property(property="test_date", type="string", format="date-time", example="2024-01-15T14:30:00Z")
 *                             )
 *                         )
 *                     )
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
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class MyTestHistory {}
