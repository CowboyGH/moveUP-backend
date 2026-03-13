<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Post(
 *     path="/api/guest/tests/{testing}/start",
 *     summary="Начать прохождение теста для гостя",
 *     description="Создаёт новую попытку прохождения теста в Redis и возвращает первое упражнение. Guest ID сохраняется в cookie.",
 *     tags={"Guest Tests"},
 *     @OA\Parameter(
 *         name="testing",
 *         in="path",
 *         required=true,
 *         description="ID теста",
 *         @OA\Schema(type="integer", example=8)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Тест успешно начат для гостя",
 *         @OA\Header(
 *             header="Set-Cookie",
 *             description="guest_id сохраняется в cookie",
 *             @OA\Schema(type="string", example="guest_id=550e8400-e29b-41d4-a716-446655440000; expires=Thu, 12-Apr-2026 14:30:00 GMT; path=/; httponly")
 *         ),
 *         @OA\JsonContent(ref="#/components/schemas/GuestTestStartResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Тест недоступен",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="string", example="forbidden"),
 *             @OA\Property(property="message", type="string", example="Этот тест недоступен")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="В тесте нет упражнений",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="string", example="not_found"),
 *             @OA\Property(property="message", type="string", example="В этом тесте нет упражнений")
 *         )
 *     )
 * )
 */
class GuestTestPaths {}

/**
 * @OA\Post(
 *     path="/api/guest/test-attempts/{attempt}/result",
 *     summary="Сохранить результат упражнения для гостя",
 *     description="Сохраняет результат текущего упражнения в Redis. Если это последнее упражнение, возвращает флаг all_exercises_completed.",
 *     tags={"Guest Tests"},
 *     @OA\Parameter(
 *         name="attempt",
 *         in="path",
 *         required=true,
 *         description="ID попытки (с префиксом guest_)",
 *         @OA\Schema(type="string", example="guest_816a590b-e438-49d8-962e-678e80849e58")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"testing_exercise_id", "result_value"},
 *             @OA\Property(property="testing_exercise_id", type="integer", example=16, description="ID тестового упражнения"),
 *             @OA\Property(property="result_value", type="integer", example=2, description="Оценка от 1 до 4", minimum=1, maximum=4)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Результат сохранён",
 *         @OA\JsonContent(ref="#/components/schemas/GuestTestStoreResultResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Попытка не найдена",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="string", example="not_found"),
 *             @OA\Property(property="message", type="string", example="Активная попытка теста не найдена")
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Конфликт",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="code", type="string", example="conflict"),
 *                     @OA\Property(property="message", type="string", example="Упражнение не принадлежит этому тесту")
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="code", type="string", example="conflict"),
 *                     @OA\Property(property="message", type="string", example="Результат для этого упражнения уже сохранён")
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="string", example="validation_failed"),
 *             @OA\Property(property="message", type="string", example="Ошибка валидации"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "result_value": {"Значение должно быть от 1 до 4"}
 *                 }
 *             )
 *         )
 *     )
 * )
 */
class GuestTestStoreResult {}

/**
 * @OA\Post(
 *     path="/api/guest/test-attempts/{attempt}/complete",
 *     summary="Завершить тест для гостя",
 *     description="Завершает попытку, сохраняет пульс и помечает тест как завершённый в Redis",
 *     tags={"Guest Tests"},
 *     @OA\Parameter(
 *         name="attempt",
 *         in="path",
 *         required=true,
 *         description="ID попытки (с префиксом guest_)",
 *         @OA\Schema(type="string", example="guest_816a590b-e438-49d8-962e-678e80849e58")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pulse"},
 *             @OA\Property(property="pulse", type="integer", example=151, description="Пульс после выполнения теста", minimum=30, maximum=220)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Тест успешно завершён",
 *         @OA\JsonContent(ref="#/components/schemas/GuestTestCompleteResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Попытка не найдена или тест не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="string", example="not_found"),
 *             @OA\Property(property="message", type="string", example="Активная попытка теста не найдена")
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Не все упражнения выполнены",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="string", example="conflict"),
 *             @OA\Property(property="message", type="string", example="Не все упражнения выполнены. Осталось: 2")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="string", example="validation_failed"),
 *             @OA\Property(property="message", type="string", example="Ошибка валидации"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "pulse": {"Пульс должен быть от 30 до 220"}
 *                 }
 *             )
 *         )
 *     )
 * )
 */
class GuestTestComplete {}

/**
 * @OA\Get(
 *     path="/api/guest/tests/history",
 *     summary="Получить историю тестов гостя",
 *     description="Возвращает статистику и список всех завершённых попыток гостя из Redis",
 *     tags={"Guest Tests"},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. История тестов гостя",
 *         @OA\JsonContent(ref="#/components/schemas/GuestTestHistoryResponse")
 *     )
 * )
 */
class GuestTestHistory {}

/**
 * @OA\Delete(
 *     path="/api/guest/tests/reset",
 *     summary="Сбросить результаты тестов гостя",
 *     description="Удаляет все результаты тестов гостя из Redis",
 *     tags={"Guest Tests"},
 *     @OA\Response(
 *         response=200,
 *         description="Результаты успешно сброшены",
 *         @OA\JsonContent(ref="#/components/schemas/GuestTestResetResponse")
 *     )
 * )
 */
class GuestTestReset {}
