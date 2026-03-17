<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Get(
 *     path="/api/workouts",
 *     summary="Получить список тренировок пользователя",
 *     description="Возвращает список тренировок пользователя, сгруппированный по статусам assigned и started",
 *     tags={"Workouts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(property="data", ref="#/components/schemas/WorkoutsListResponse")
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
class WorkoutPaths {}

/**
 * @OA\Get(
 *     path="/my-workout-history",
 *     summary="Получить историю тренировок пользователя",
 *     description="Возвращает историю тренировок пользователя с детальной информацией о прогрессе",
 *     tags={"Workouts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(property="data", ref="#/components/schemas/WorkoutHistoryResponse")
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
class WorkoutHistoryPaths {}
