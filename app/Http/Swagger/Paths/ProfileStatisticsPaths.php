<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Get(
 *     path="/api/profile/statistics",
 *     summary="Получить всю статистику профиля",
 *     description="Возвращает текущую фазу пользователя, статистику объема, тренда и частоты",
 *     operationId="getProfileStatistics",
 *     tags={"Profile Statistics"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="exercise_id",
 *         in="query",
 *         description="ID упражнения для статистики объема (если не указан, берется последнее использованное)",
 *         required=false,
 *         @OA\Schema(type="integer", example=17)
 *     ),
 *     @OA\Parameter(
 *         name="week_offset",
 *         in="query",
 *         description="Смещение недели для объема (0 - текущая, 1 - прошлая и т.д.)",
 *         required=false,
 *         @OA\Schema(type="integer", default=0, minimum=0, example=0)
 *     ),
 *     @OA\Parameter(
 *         name="workout_id",
 *         in="query",
 *         description="ID тренировки для статистики тренда (если не указан, берется последняя)",
 *         required=false,
 *         @OA\Schema(type="integer", example=231)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(ref="#/components/schemas/ProfileStatisticsResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     )
 * )
 */
class ProfileStatisticsPaths {}

/**
 * @OA\Get(
 *     path="/api/profile/statistics/volume",
 *     summary="Получить статистику объема",
 *     description="Возвращает объем тренировок по дням недели для указанного упражнения",
 *     operationId="getVolumeStatistics",
 *     tags={"Profile Statistics"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="exercise_id",
 *         in="query",
 *         description="ID упражнения (если не указан, берется последнее использованное)",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="week_offset",
 *         in="query",
 *         description="Смещение недели (0 - текущая, 1 - прошлая и т.д.)",
 *         required=false,
 *         @OA\Schema(type="integer", default=0, minimum=0, example=0)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(ref="#/components/schemas/VolumeResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Упражнение не найдено",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class ProfileStatisticsVolumePaths {}

/**
 * @OA\Get(
 *     path="/api/profile/statistics/trend",
 *     summary="Получить статистику тренда",
 *     description="Возвращает оценки упражнений из выбранной тренировки",
 *     operationId="getTrendStatistics",
 *     tags={"Profile Statistics"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="workout_id",
 *         in="query",
 *         description="ID тренировки (если не указан, берется последняя)",
 *         required=false,
 *         @OA\Schema(type="integer", example=231)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(ref="#/components/schemas/TrendResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Тренировка не найдена или не принадлежит пользователю",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class ProfileStatisticsTrendPaths {}

/**
 * @OA\Get(
 *     path="/api/profile/statistics/frequency",
 *     summary="Получить статистику частоты",
 *     description="Возвращает количество тренировок по неделям за выбранный период",
 *     operationId="getFrequencyStatistics",
 *     tags={"Profile Statistics"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="period",
 *         in="query",
 *         description="Период для статистики",
 *         required=false,
 *         @OA\Schema(
 *             type="string",
 *             enum={"week", "month", "3months", "6months", "year"},
 *             default="month",
 *             example="month"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="offset",
 *         in="query",
 *         description="Смещение периода (0 - текущий, 1 - прошлый и т.д.)",
 *         required=false,
 *         @OA\Schema(type="integer", default=0, minimum=0, example=0)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(ref="#/components/schemas/FrequencyResponse")
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
 *     )
 * )
 */
class ProfileStatisticsFrequencyPaths {}

/**
 * @OA\Get(
 *     path="/api/profile/statistics/exercises",
 *     summary="Получить список упражнений",
 *     description="Возвращает список упражнений, которые пользователь когда-либо выполнял",
 *     operationId="getProfileExercisesList",
 *     tags={"Profile Statistics"},
 *     security={{"bearerAuth":{}}},
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
 *                 @OA\Items(ref="#/components/schemas/ProfileExerciseItem")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     )
 * )
 */
class ProfileStatisticsExercisesPaths {}

/**
 * @OA\Get(
 *     path="/api/profile/statistics/workouts",
 *     summary="Получить список тренировок",
 *     description="Возвращает список завершенных тренировок пользователя для выбора в статистике тренда",
 *     operationId="getProfileWorkoutsList",
 *     tags={"Profile Statistics"},
 *     security={{"bearerAuth":{}}},
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
 *                 @OA\Items(ref="#/components/schemas/ProfileWorkoutItem")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     )
 * )
 */
class ProfileStatisticsWorkoutsPaths {}
