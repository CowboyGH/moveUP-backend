<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Post(
 *     path="/api/user-parameters/goal",
 *     summary="Сохранить цель тренировок (шаг 1)",
 *     tags={"User Parameters"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"goal_id"},
 *             @OA\Property(property="goal_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Цель сохранена для гостя",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Цель сохранена для гостя"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="guest_id", type="string", example="2daabfb2-cd72-440c-aacd-00f08d5e92e3"),
 *                 @OA\Property(
 *                     property="guest_data",
 *                     type="object",
 *                     @OA\Property(property="goal_id", type="integer", example=1)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class UserParameterPaths {}

/**
 * @OA\Post(
 *     path="/api/user-parameters/anthropometry",
 *     summary="Сохранить антропометрические данные (шаг 2)",
 *     tags={"User Parameters"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"gender", "age", "weight", "height", "equipment_id"},
 *             @OA\Property(property="gender", type="string", enum={"male", "female"}, example="female"),
 *             @OA\Property(property="age", type="integer", example=32),
 *             @OA\Property(property="weight", type="number", format="float", example=50),
 *             @OA\Property(property="height", type="integer", example=172),
 *             @OA\Property(property="equipment_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Антропометрия сохранена для гостя",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Антропометрия сохранена для гостя"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="guest_id", type="string", example="2daabfb2-cd72-440c-aacd-00f08d5e92e3"),
 *                 @OA\Property(
 *                     property="guest_data",
 *                     type="object",
 *                     @OA\Property(property="goal_id", type="integer", example=1),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-24 12:10:56"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-24 12:10:56"),
 *                     @OA\Property(property="gender", type="string", example="female"),
 *                     @OA\Property(property="age", type="integer", example=32),
 *                     @OA\Property(property="weight", type="number", format="float", example=50),
 *                     @OA\Property(property="height", type="integer", example=172),
 *                     @OA\Property(property="equipment_id", type="integer", example=1)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class SaveAnthropometry {}

/**
 * @OA\Post(
 *     path="/api/user-parameters/level",
 *     summary="Сохранить уровень подготовки (шаг 3)",
 *     tags={"User Parameters"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"level_id"},
 *             @OA\Property(property="level_id", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Уровень сохранен для гостя",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Уровень сохранен для гостя"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="guest_id", type="string", example="2daabfb2-cd72-440c-aacd-00f08d5e92e3"),
 *                 @OA\Property(
 *                     property="guest_data",
 *                     type="object",
 *                     @OA\Property(property="goal_id", type="integer", example=1),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-24 12:10:56"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-24 12:11:10"),
 *                     @OA\Property(property="gender", type="string", example="female"),
 *                     @OA\Property(property="age", type="integer", example=32),
 *                     @OA\Property(property="weight", type="number", format="float", example=50),
 *                     @OA\Property(property="height", type="integer", example=172),
 *                     @OA\Property(property="equipment_id", type="integer", example=1),
 *                     @OA\Property(property="level_id", type="integer", example=3)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class SaveLevel {}

/**
 * @OA\Get(
 *     path="/api/user-parameters/me",
 *     summary="Получить параметры авторизованного пользователя",
 *     tags={"User Parameters"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Параметры получены",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Параметры получены"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=54),
 *                 @OA\Property(property="user_id", type="integer", example=336),
 *                 @OA\Property(property="equipment_id", type="integer", example=1),
 *                 @OA\Property(property="level_id", type="integer", example=1),
 *                 @OA\Property(property="goal_id", type="integer", example=1),
 *                 @OA\Property(property="height", type="integer", example=172),
 *                 @OA\Property(property="weight", type="number", format="float", example=50),
 *                 @OA\Property(property="age", type="integer", example=22),
 *                 @OA\Property(property="gender", type="string", enum={"male", "female"}, example="female"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-24T04:26:24.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-24T04:26:24.000000Z"),
 *                 @OA\Property(
 *                     property="goal",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Рост силовых показателей"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z")
 *                 ),
 *                 @OA\Property(
 *                     property="level",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Начинающий"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z")
 *                 ),
 *                 @OA\Property(
 *                     property="equipment",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Зал"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z")
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
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class GetMyParameters {}

/**
 * @OA\Put(
 *     path="/api/user-parameters",
 *     summary="Обновить параметры пользователя",
 *     tags={"User Parameters"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="goal_id", type="integer", example=2),
 *             @OA\Property(property="level_id", type="integer", example=2),
 *             @OA\Property(property="equipment_id", type="integer", example=2),
 *             @OA\Property(property="height", type="integer", example=185),
 *             @OA\Property(property="weight", type="number", format="float", example=80.5),
 *             @OA\Property(property="age", type="integer", example=26),
 *             @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Параметры обновлены",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Параметры обновлены"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=51),
 *                 @OA\Property(property="user_id", type="integer", example=332),
 *                 @OA\Property(property="equipment_id", type="integer", example=2),
 *                 @OA\Property(property="level_id", type="integer", example=2),
 *                 @OA\Property(property="goal_id", type="integer", example=2),
 *                 @OA\Property(property="height", type="integer", example=185),
 *                 @OA\Property(property="weight", type="number", format="float", example=80.5),
 *                 @OA\Property(property="age", type="integer", example=26),
 *                 @OA\Property(property="gender", type="string", example="male"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-24T02:58:19.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-24T03:02:07.000000Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class UpdateUserParameters {}

/**
 * @OA\Delete(
 *     path="/api/user-parameters/guest",
 *     summary="Очистить данные гостя",
 *     tags={"User Parameters"},
 *     @OA\Response(
 *         response=200,
 *         description="Данные гостя очищены",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Данные гостя очищены")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class ClearGuestData {}
