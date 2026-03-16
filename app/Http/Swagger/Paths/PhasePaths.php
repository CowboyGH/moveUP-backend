<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Get(
 *     path="/api/user/current-phase",
 *     summary="Получить текущую фазу пользователя с прогрессом",
 *     tags={"Phases"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. Данные о текущей фазе и прогрессе",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Текущая фаза пользователя"),
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/UserPhaseProgress"
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
class PhasePaths {}

/**
 * @OA\Get(
 *     path="/api/phases",
 *     summary="Получить список всех фаз",
 *     tags={"Phases"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. Список всех фаз",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Список всех фаз"),
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/PhaseList"
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
class GetAllPhases {}

/**
 * @OA\Get(
 *     path="/api/phases/{phase}",
 *     summary="Получить детальную информацию о фазе с тренировками",
 *     tags={"Phases"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="phase",
 *         in="path",
 *         required=true,
 *         description="ID фазы",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. Детальная информация о фазе с привязанными тренировками",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Детальная информация о фазе"),
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/PhaseWithWorkouts"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Фаза не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class GetPhaseDetails {}

/**
 * @OA\Post(
 *     path="/api/user/weekly-goal",
 *     summary="Обновить недельную цель тренировок",
 *     tags={"Phases"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/UpdateWeeklyGoalRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Цель успешно обновлена",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Недельная цель обновлена"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="weekly_goal", type="integer", example=5)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorWeeklyGoal")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="У пользователя нет активной фазы",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="У пользователя нет активной фазы")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class UpdateWeeklyGoal {}
