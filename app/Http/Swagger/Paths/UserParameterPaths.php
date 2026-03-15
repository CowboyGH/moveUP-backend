<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Get(
 *     path="/api/goals",
 *     summary="Получить список всех целей тренировок",
 *     tags={"User Parameters"},
 *     @OA\Response(
 *         response=200,
 *         description="Список целей получен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Список целей получен"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Рост силовых показателей"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z")
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
class UserParameterPaths {}

/**
 * @OA\Get(
 *     path="/api/levels",
 *     summary="Получить список всех уровней подготовки",
 *     tags={"User Parameters"},
 *     @OA\Response(
 *         response=200,
 *         description="Список уровней получен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Список уровней получен"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Начинающий"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z")
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
class GetLevels {}

/**
 * @OA\Get(
 *     path="/api/equipment",
 *     summary="Получить список всего доступного оборудования",
 *     tags={"User Parameters"},
 *     @OA\Response(
 *         response=200,
 *         description="Список оборудования получен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Список оборудования получен"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z")
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
class GetEquipment {}

/**
 * @OA\Get(
 *     path="/api/user-parameters/references",
 *     summary="Получить все справочные данные сразу (цели, уровни, оборудование)",
 *     description="Полезно для первоначальной загрузки приложения, чтобы сократить количество запросов",
 *     tags={"User Parameters"},
 *     @OA\Response(
 *         response=200,
 *         description="Справочные данные получены",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Справочные данные получены"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="goals",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Рост силовых показателей")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="levels",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Начинающий")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="equipment",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Тренажерный зал")
 *                     )
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
class GetAllReferences {}

/**
 * @OA\Post(
 *     path="/api/user-parameters/goal",
 *     summary="Сохранить цель тренировок (шаг 1)",
 *     description="Сохраняет цель тренировок для авторизованного пользователя или гостя",
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
 *         description="Цель сохранена",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="Цель сохранена"),
 *                     @OA\Property(
 *                         property="data",
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=54),
 *                         @OA\Property(property="user_id", type="integer", example=336),
 *                         @OA\Property(property="goal_id", type="integer", example=1),
 *                         @OA\Property(property="updated_at", type="string", format="date-time"),
 *                         @OA\Property(property="created_at", type="string", format="date-time")
 *                     )
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="Цель сохранена для гостя"),
 *                     @OA\Property(
 *                         property="data",
 *                         type="object",
 *                         @OA\Property(property="guest_id", type="string", example="2daabfb2-cd72-440c-aacd-00f08d5e92e3"),
 *                         @OA\Property(
 *                             property="guest_data",
 *                             type="object",
 *                             @OA\Property(property="goal_id", type="integer", example=1)
 *                         )
 *                     )
 *                 )
 *             }
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
class SaveGoal {}

/**
 * @OA\Post(
 *     path="/api/user-parameters/anthropometry",
 *     summary="Сохранить антропометрические данные (шаг 2)",
 *     description="Сохраняет антропометрические данные для авторизованного пользователя или гостя",
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
 *         description="Антропометрия сохранена",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="Антропометрия сохранена"),
 *                     @OA\Property(
 *                         property="data",
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=54),
 *                         @OA\Property(property="user_id", type="integer", example=336),
 *                         @OA\Property(property="gender", type="string", example="female"),
 *                         @OA\Property(property="age", type="integer", example=32),
 *                         @OA\Property(property="weight", type="number", example=50),
 *                         @OA\Property(property="height", type="integer", example=172),
 *                         @OA\Property(property="equipment_id", type="integer", example=1)
 *                     )
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="Антропометрия сохранена для гостя"),
 *                     @OA\Property(
 *                         property="data",
 *                         type="object",
 *                         @OA\Property(property="guest_id", type="string", example="2daabfb2-cd72-440c-aacd-00f08d5e92e3"),
 *                         @OA\Property(
 *                             property="guest_data",
 *                             type="object",
 *                             @OA\Property(property="goal_id", type="integer", example=1),
 *                             @OA\Property(property="gender", type="string", example="female"),
 *                             @OA\Property(property="age", type="integer", example=32),
 *                             @OA\Property(property="weight", type="number", example=50),
 *                             @OA\Property(property="height", type="integer", example=172),
 *                             @OA\Property(property="equipment_id", type="integer", example=1)
 *                         )
 *                     )
 *                 )
 *             }
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
 *     description="Сохраняет уровень подготовки для авторизованного пользователя или гостя",
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
 *         description="Уровень сохранен",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="Уровень сохранен"),
 *                     @OA\Property(
 *                         property="data",
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=54),
 *                         @OA\Property(property="user_id", type="integer", example=336),
 *                         @OA\Property(property="level_id", type="integer", example=3)
 *                     )
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="Уровень сохранен для гостя"),
 *                     @OA\Property(
 *                         property="data",
 *                         type="object",
 *                         @OA\Property(property="guest_id", type="string", example="2daabfb2-cd72-440c-aacd-00f08d5e92e3"),
 *                         @OA\Property(
 *                             property="guest_data",
 *                             type="object",
 *                             @OA\Property(property="goal_id", type="integer", example=1),
 *                             @OA\Property(property="gender", type="string", example="female"),
 *                             @OA\Property(property="age", type="integer", example=32),
 *                             @OA\Property(property="weight", type="number", example=50),
 *                             @OA\Property(property="height", type="integer", example=172),
 *                             @OA\Property(property="equipment_id", type="integer", example=1),
 *                             @OA\Property(property="level_id", type="integer", example=3)
 *                         )
 *                     )
 *                 )
 *             }
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
 *     description="Возвращает параметры текущего авторизованного пользователя с связанными моделями цели, уровня и оборудования",
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
 *                     @OA\Property(property="description", type="string", example="Тренировки направлены на увеличение силы"),
 *                     @OA\Property(property="icon", type="string", nullable=true, example="goals/strength.png"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z")
 *                 ),
 *                 @OA\Property(
 *                     property="level",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Начинающий"),
 *                     @OA\Property(property="description", type="string", example="Для тех, кто только начинает"),
 *                     @OA\Property(property="icon", type="string", nullable=true, example="levels/beginner.png"),
 *                     @OA\Property(property="min_experience_months", type="integer", example=0),
 *                     @OA\Property(property="max_experience_months", type="integer", example=6),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-20T08:20:33.000000Z")
 *                 ),
 *                 @OA\Property(
 *                     property="equipment",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Тренажерный зал"),
 *                     @OA\Property(property="description", type="string", example="Полный набор тренажеров"),
 *                     @OA\Property(property="icon", type="string", nullable=true, example="equipment/gym.png"),
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
 *     description="Обновляет один или несколько параметров авторизованного пользователя. При изменении ключевых параметров (goal_id, level_id, equipment_id) автоматически перегенерируются тренировки",
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
 *     description="Удаляет все данные гостя из Redis и очищает cookie guest_id",
 *     tags={"User Parameters"},
 *     @OA\Response(
 *         response=200,
 *         description="Данные гостя очищены",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Данные гостя очищены"),
 *             @OA\Property(property="data", type="null", example=null)
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
