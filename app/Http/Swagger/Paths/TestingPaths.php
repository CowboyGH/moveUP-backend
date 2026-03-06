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
 * @OA\Post(
 *     path="/api/tests/{testing}/start",
 *     summary="Начать прохождение теста",
 *     description="Создаёт новую попытку прохождения теста и возвращает первое упражнение",
 *     tags={"Testings"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="testing",
 *         in="path",
 *         required=true,
 *         description="ID теста",
 *         @OA\Schema(type="integer", example=4)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Тест успешно начат",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Тест начат"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="attempt_id", type="integer", example=11),
 *                 @OA\Property(
 *                     property="testing",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=4),
 *                     @OA\Property(property="title", type="string", example="impedit illum maiores aut"),
 *                     @OA\Property(property="description", type="string", example="Maiores occaecati repudiandae eos recusandae."),
 *                     @OA\Property(property="duration_minutes", type="string", example="38"),
 *                     @OA\Property(property="image", type="string", example="tests/"),
 *                     @OA\Property(property="total_exercises", type="integer", example=4)
 *                 ),
 *                 @OA\Property(
 *                     property="current_exercise",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=20),
 *                     @OA\Property(property="description", type="string", example="Harum non est ea fugiat non magnam architecto."),
 *                     @OA\Property(property="image", type="string", example="testing_exercises/"),
 *                     @OA\Property(property="order_number", type="integer", example=1)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Тест недоступен",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="В тесте нет упражнений",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     )
 * )
 */
class StartTestAttempt {}

/**
 * @OA\Post(
 *     path="/api/test-attempts/{attempt}/result",
 *     summary="Сохранить результат выполнения упражнения",
 *     description="Сохраняет результат текущего упражнения и возвращает следующее (или сигнал о завершении)",
 *     tags={"Testings"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="attempt",
 *         in="path",
 *         required=true,
 *         description="ID попытки",
 *         @OA\Schema(type="integer", example=11)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"testing_exercise_id", "result_value"},
 *             @OA\Property(property="testing_exercise_id", type="integer", example=18, description="ID тестового упражнения"),
 *             @OA\Property(property="result_value", type="integer", example=4, description="Оценка от 1 до 4")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Результат сохранён. Возвращается следующее упражнение или признак завершения теста.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Результат сохранён"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="saved", type="boolean", example=true),
 *                 @OA\Property(
 *                     property="result",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=12),
 *                     @OA\Property(property="user_id", type="integer", example=2),
 *                     @OA\Property(property="testing_id", type="integer", example=4),
 *                     @OA\Property(property="test_attempt_id", type="integer", example=11),
 *                     @OA\Property(property="testing_exercise_id", type="integer", example=18),
 *                     @OA\Property(property="result_value", type="integer", example=4),
 *                     @OA\Property(property="test_date", type="string", format="date", example="2026-03-05"),
 *                     @OA\Property(property="created_at", type="string", format="datetime", example="2026-03-06T03:30:15.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="datetime", example="2026-03-06T03:30:15.000000Z")
 *                 ),
 *                 @OA\Property(
 *                     property="next_exercise",
 *                     type="object",
 *                     nullable=true,
 *                     description="Следующее упражнение (если есть)",
 *                     @OA\Property(property="id", type="integer", example=20),
 *                     @OA\Property(property="description", type="string", example="Harum non est ea fugiat non magnam architecto."),
 *                     @OA\Property(property="image", type="string", example="testing_exercises/"),
 *                     @OA\Property(property="order_number", type="integer", example=2)
 *                 ),
 *                 @OA\Property(
 *                     property="all_exercises_completed",
 *                     type="boolean",
 *                     nullable=true,
 *                     example=true,
 *                     description="Флаг, что все упражнения выполнены"
 *                 ),
 *                 @OA\Property(
 *                     property="message",
 *                     type="string",
 *                     nullable=true,
 *                     example="Все упражнения выполнены. Введите пульс для завершения теста.",
 *                     description="Сообщение для случая завершения теста"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Конфликт (тест уже завершён, упражнение не принадлежит тесту, результат уже сохранён)",
 *         @OA\JsonContent(ref="#/components/schemas/ConflictResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     )
 * )
 */
class StoreTestResult {}

/**
 * @OA\Post(
 *     path="/api/test-attempts/{attempt}/complete",
 *     summary="Завершить тест и сохранить пульс",
 *     description="Завершает попытку, сохраняет пульс и проставляет дату завершения",
 *     tags={"Testings"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="attempt",
 *         in="path",
 *         required=true,
 *         description="ID попытки",
 *         @OA\Schema(type="integer", example=11)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pulse"},
 *             @OA\Property(property="pulse", type="integer", example=172, description="Пульс после выполнения теста")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Тест успешно завершён",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Тест успешно завершён"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="attempt_id", type="integer", example=11),
 *                 @OA\Property(property="completed_at", type="string", format="datetime", example="2026-03-06T03:31:30.000000Z"),
 *                 @OA\Property(property="pulse", type="integer", example=172)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Конфликт (тест уже завершён, не все упражнения выполнены)",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="string", example="conflict"),
 *             @OA\Property(property="message", type="string", example="Не все упражнения выполнены. Осталось: 3")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     )
 * )
 */
class CompleteTestAttempt {}

/**
 * @OA\Get(
 *     path="/api/my-test-history",
 *     summary="Получить историю пройденных тестов текущего пользователя",
 *     description="Возвращает статистику и список всех завершённых попыток с результатами упражнений",
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
 *                     @OA\Property(property="total_attempts", type="integer", example=1),
 *                     @OA\Property(property="unique_tests_completed", type="integer", example=1),
 *                     @OA\Property(property="last_test_date", type="string", format="datetime", example="2026-03-06 10:31:30")
 *                 ),
 *                 @OA\Property(
 *                     property="history",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="attempt_id", type="integer", example=11),
 *                         @OA\Property(property="testing_id", type="integer", example=4),
 *                         @OA\Property(property="testing_title", type="string", example="impedit illum maiores aut"),
 *                         @OA\Property(property="completed_at", type="string", format="datetime", example="2026-03-06 10:31:30"),
 *                         @OA\Property(property="pulse", type="integer", example=172),
 *                         @OA\Property(
 *                             property="exercises_results",
 *                             type="array",
 *                             @OA\Items(
 *                                 @OA\Property(property="testing_exercise_id", type="integer", example=20),
 *                                 @OA\Property(property="exercise_id", type="integer", example=10),
 *                                 @OA\Property(property="exercise_description", type="string", example="Eos id atque et nulla."),
 *                                 @OA\Property(property="result_value", type="integer", example=3),
 *                                 @OA\Property(property="test_date", type="string", format="date", example="2026-03-06")
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
