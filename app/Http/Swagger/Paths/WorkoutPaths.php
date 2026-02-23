<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Get(
 *     path="/api/workouts",
 *     summary="Получить список всех активных тренировок",
 *     tags={"Workouts"},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. Список доступных тренировок",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="title", type="string", example="Функциональный тренинг"),
 *                     @OA\Property(property="description", type="string", example="Accusamus enim placeat nihil ad ex ipsum iste animi. Facilis ut laudantium fuga ex fugiat. Occaecati non laboriosam facere."),
 *                     @OA\Property(property="duration_minutes", type="string", example="59"),
 *                     @OA\Property(
 *                         property="phase",
 *                         type="object",
 *                         nullable=true,
 *                         @OA\Property(property="id", type="integer", example=2),
 *                         @OA\Property(property="name", type="string", example="Базовая фаза")
 *                     ),
 *                     @OA\Property(property="exercises_count", type="integer", example=0),
 *                     @OA\Property(property="warmups_count", type="integer", example=1)
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
class WorkoutPaths {}

/**
 * @OA\Get(
 *     path="/api/workouts/{id}",
 *     summary="Получить информацию о конкретной тренировке",
 *     tags={"Workouts"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID тренировки",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. Информация о тренировке",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Функциональный тренинг"),
 *                 @OA\Property(property="description", type="string", example="Accusamus enim placeat nihil ad ex ipsum iste animi. Facilis ut laudantium fuga ex fugiat. Occaecati non laboriosam facere."),
 *                 @OA\Property(property="duration_minutes", type="integer", example=59),
 *                 @OA\Property(
 *                     property="phase",
 *                     type="object",
 *                     nullable=true,
 *                     @OA\Property(property="id", type="integer", example=2),
 *                     @OA\Property(property="name", type="string", example="Базовая фаза")
 *                 ),
 *                 @OA\Property(
 *                     property="exercises",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Приседания"),
 *                         @OA\Property(property="description", type="string", example="Техника приседаний"),
 *                         @OA\Property(property="image", type="string", example="exercise.jpg", nullable=true),
 *                         @OA\Property(property="sets", type="integer", example=3),
 *                         @OA\Property(property="reps", type="integer", example=12),
 *                         @OA\Property(property="order_number", type="integer", example=1)
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="warmups",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Разминка"),
 *                         @OA\Property(property="description", type="string", example="Общая разминка"),
 *                         @OA\Property(property="image", type="string", example="warmup.jpg", nullable=true),
 *                         @OA\Property(property="order_number", type="integer", example=1)
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Тренировка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class WorkoutShow {}

/**
 * @OA\Get(
 *     path="/api/my-workout-history",
 *     summary="Получить историю пройденных тренировок текущего пользователя",
 *     tags={"Workouts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. История тренировок пользователя",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="active",
 *                     type="object",
 *                     nullable=true,
 *                     description="Текущая активная тренировка (если есть)",
 *                     example=null
 *                 ),
 *                 @OA\Property(
 *                     property="statistics",
 *                     type="object",
 *                     @OA\Property(property="total_workouts_started", type="integer", example=1),
 *                     @OA\Property(property="total_workouts_completed", type="integer", example=1),
 *                     @OA\Property(property="total_workouts_in_progress", type="integer", example=0),
 *                     @OA\Property(property="last_workout_date", type="string", format="date-time", example="2026-02-18 08:46:59")
 *                 ),
 *                 @OA\Property(
 *                     property="history",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(
 *                             property="workout",
 *                             type="object",
 *                             @OA\Property(property="id", type="integer", example=41),
 *                             @OA\Property(property="title", type="string", example="Йога для гибкости и релаксации")
 *                         ),
 *                         @OA\Property(property="started_at", type="string", format="date-time", example="2026-02-08 01:31:49"),
 *                         @OA\Property(property="completed_at", type="string", format="date-time", example="2026-02-18 08:46:59"),
 *                         @OA\Property(property="status", type="string", enum={"in_progress", "completed", "cancelled"}, example="completed"),
 *                         @OA\Property(property="duration", type="integer", example=14835),
 *                         @OA\Property(
 *                             property="progress",
 *                             type="object",
 *                             @OA\Property(property="exercises_completed", type="integer", example=0),
 *                             @OA\Property(property="exercises_total", type="integer", example=0),
 *                             @OA\Property(property="warmups_completed", type="integer", example=0),
 *                             @OA\Property(property="warmups_total", type="integer", example=0)
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
class MyWorkoutHistory {}
