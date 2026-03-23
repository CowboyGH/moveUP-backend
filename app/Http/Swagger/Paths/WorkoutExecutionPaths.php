<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Get(
 *     path="/api/workout-execution/{userWorkout}",
 *     summary="Получить детали тренировки для выполнения",
 *     description="Возвращает детальную информацию о тренировке, включая разминки и упражнения с весами пользователя",
 *     tags={"Workout Execution"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userWorkout",
 *         in="path",
 *         required=true,
 *         description="ID записи тренировки пользователя",
 *         @OA\Schema(type="integer", example=926)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Детали тренировки",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Детали тренировки"),
 *             @OA\Property(property="data", ref="#/components/schemas/WorkoutDetailsResponse")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Тренировка не принадлежит пользователю",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Тренировка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     )
 * )
 */
class WorkoutExecutionPaths {}

/**
 * @OA\Post(
 *     path="/api/workouts/start",
 *     summary="Начать тренировку",
 *     description="Меняет статус тренировки с assigned на started и возвращает первое упражнение (или разминку)",
 *     tags={"Workout Execution"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"workout_id"},
 *             @OA\Property(property="workout_id", type="integer", example=1),
 *             @OA\Property(property="with_warmup", type="boolean", example=true, description="Выбрать разминку (опционально)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Тренировка начата",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="Разминка начата"),
 *                     @OA\Property(
 *                         property="data",
 *                         type="object",
 *                         @OA\Property(property="user_workout_id", type="integer", example=815),
 *                         @OA\Property(property="type", type="string", example="warmup"),
 *                         @OA\Property(
 *                             property="warmup",
 *                             type="object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="Дыхание: Капалабхати"),
 *                             @OA\Property(property="description", type="string", example="Очистительное дыхание: короткие активные выдохи, пассивные вдохи. Разогревает и тонизирует."),
 *                             @OA\Property(property="image", type="string", example="http://localhost:8000/storage/warmups/breathing-kapalabhati.jpg"),
 *                             @OA\Property(property="duration_seconds", type="integer", example=60),
 *                             @OA\Property(property="order_number", type="integer", example=1),
 *                             @OA\Property(property="is_last", type="boolean", example=false)
 *                         ),
 *                         @OA\Property(property="total_warmups", type="integer", example=2)
 *                     )
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="Тренировка начата"),
 *                     @OA\Property(
 *                         property="data",
 *                         type="object",
 *                         @OA\Property(property="user_workout_id", type="integer", example=815),
 *                         @OA\Property(property="started_at", type="string", format="date-time", example="2026-03-22T09:08:14.000000Z"),
 *                         @OA\Property(property="type", type="string", example="exercise"),
 *                         @OA\Property(property="needs_weight_input", type="boolean", example=true),
 *                         @OA\Property(
 *                             property="exercise",
 *                             ref="#/components/schemas/ExerciseItem"
 *                         )
 *                     )
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Тренировка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Уже есть активная тренировка",
 *         @OA\JsonContent(ref="#/components/schemas/ConflictResponse")
 *     )
 * )
 */
class WorkoutExecutionStartPath {}

/**
 * @OA\Post(
 *     path="/api/workout-execution/{userWorkout}/next-warmup",
 *     summary="Получить следующее упражнение разминки",
 *     description="Возвращает следующее упражнение разминки. Если передать current_warmup_id = null или не передать, вернет первое упражнение. При завершении разминки автоматически переходит к основной тренировке.",
 *     tags={"Workout Execution"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userWorkout",
 *         in="path",
 *         required=true,
 *         description="ID записи тренировки пользователя",
 *         @OA\Schema(type="integer", example=815)
 *     ),
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="current_warmup_id",
 *                 type="integer",
 *                 nullable=true,
 *                 description="ID текущего выполненного упражнения разминки. Если не указан или null, возвращается первое упражнение.",
 *                 example=1
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. Возвращает следующее упражнение разминки или переход к основной тренировке",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="success"),
 *                     @OA\Property(
 *                         property="data",
 *                         type="object",
 *                         @OA\Property(property="type", type="string", example="warmup"),
 *                         @OA\Property(
 *                             property="warmup",
 *                             type="object",
 *                             @OA\Property(property="id", type="integer", example=4),
 *                             @OA\Property(property="name", type="string", example="Динамическая растяжка: Ноги"),
 *                             @OA\Property(property="description", type="string", example="Растяжка мышц ног в движении..."),
 *                             @OA\Property(property="image", type="string", example="http://localhost:8000/storage/warmups/dynamic-stretching-legs.jpg"),
 *                             @OA\Property(property="duration_seconds", type="integer", example=60),
 *                             @OA\Property(property="order_number", type="integer", example=2),
 *                             @OA\Property(property="is_last", type="boolean", example=true)
 *                         )
 *                     )
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="success"),
 *                     @OA\Property(
 *                         property="data",
 *                         type="object",
 *                         @OA\Property(property="type", type="string", example="exercise"),
 *                         @OA\Property(property="user_workout_id", type="integer", example=815),
 *                         @OA\Property(property="needs_weight_input", type="boolean", example=true),
 *                         @OA\Property(
 *                             property="exercise",
 *                             ref="#/components/schemas/ExerciseItem"
 *                         )
 *                     )
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Тренировка не принадлежит пользователю",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Тренировка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class WorkoutExecutionNextWarmupPath {}

/**
 * @OA\Post(
 *     path="/api/workout-execution/{userWorkout}/next-exercise",
 *     summary="Получить следующее упражнение",
 *     description="Возвращает следующее упражнение или сигнал о завершении тренировки",
 *     tags={"Workout Execution"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userWorkout",
 *         in="path",
 *         required=true,
 *         description="ID записи тренировки пользователя",
 *         @OA\Schema(type="integer", example=926)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"current_exercise_id"},
 *             @OA\Property(property="current_exercise_id", type="integer", example=21),
 *             @OA\Property(property="weight_used", type="number", format="float", example=40, nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="success"),
 *                     @OA\Property(property="data", ref="#/components/schemas/ExerciseResponse")
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="success"),
 *                     @OA\Property(property="data", ref="#/components/schemas/CompletedResponse")
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Тренировка не принадлежит пользователю",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
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
class WorkoutExecutionNextExercisePath {}

/**
 * @OA\Post(
 *     path="/api/workout-execution/{userWorkout}/save-exercise-result",
 *     summary="Сохранить результат выполнения упражнения",
 *     description="Сохраняет результат выполнения упражнения, включая реакцию пользователя и использованный вес",
 *     tags={"Workout Execution"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userWorkout",
 *         in="path",
 *         required=true,
 *         description="ID записи тренировки пользователя",
 *         @OA\Schema(type="integer", example=926)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/SaveExerciseResultRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Результат успешно сохранен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Результат упражнения сохранен"),
 *             @OA\Property(property="data", ref="#/components/schemas/SaveExerciseResultResponse")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Тренировка не принадлежит пользователю",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
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
class WorkoutExecutionSaveResultPath {}

/**
 * @OA\Post(
 *     path="/api/workout-execution/{userWorkout}/complete",
 *     summary="Завершить тренировку",
 *     description="Завершает тренировку, обновляет статус и прогресс пользователя",
 *     tags={"Workout Execution"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userWorkout",
 *         in="path",
 *         required=true,
 *         description="ID записи тренировки пользователя",
 *         @OA\Schema(type="integer", example=926)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Тренировка успешно завершена",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Тренировка успешно завершена!"),
 *             @OA\Property(property="data", ref="#/components/schemas/CompleteWorkoutResponse")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Тренировка не принадлежит пользователю",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Тренировка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Тренировка уже завершена",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="string", example="conflict"),
 *             @OA\Property(property="message", type="string", example="Тренировка уже завершена")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class WorkoutExecutionCompletePath {}

/**
 * @OA\Post(
 *     path="/api/workout-execution/{userWorkout}/start-warmup",
 *     summary="Начать разминку",
 *     description="Начинает разминку и возвращает первое упражнение разминки. Если у тренировки нет разминки, сразу переходит к основной тренировке.",
 *     tags={"Workout Execution"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userWorkout",
 *         in="path",
 *         required=true,
 *         description="ID записи тренировки пользователя",
 *         @OA\Schema(type="integer", example=815)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ. Возвращает первое упражнение разминки или переход к основной тренировке",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="Разминка начата"),
 *                     @OA\Property(
 *                         property="data",
 *                         type="object",
 *                         @OA\Property(property="type", type="string", example="warmup"),
 *                         @OA\Property(property="user_workout_id", type="integer", example=815),
 *                         @OA\Property(
 *                             property="warmup",
 *                             type="object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="Дыхание: Капалабхати"),
 *                             @OA\Property(property="description", type="string", example="Очистительное дыхание: короткие активные выдохи, пассивные вдохи."),
 *                             @OA\Property(property="image", type="string", example="http://localhost:8000/storage/warmups/breathing-kapalabhati.jpg"),
 *                             @OA\Property(property="duration_seconds", type="integer", example=60),
 *                             @OA\Property(property="order_number", type="integer", example=1),
 *                             @OA\Property(property="is_last", type="boolean", example=false)
 *                         ),
 *                         @OA\Property(property="total_warmups", type="integer", example=2)
 *                     )
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="Тренировка начата"),
 *                     @OA\Property(
 *                         property="data",
 *                         type="object",
 *                         @OA\Property(property="type", type="string", example="exercise"),
 *                         @OA\Property(property="user_workout_id", type="integer", example=815),
 *                         @OA\Property(property="needs_weight_input", type="boolean", example=true),
 *                         @OA\Property(
 *                             property="exercise",
 *                             ref="#/components/schemas/ExerciseItem"
 *                         )
 *                     )
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Тренировка не принадлежит пользователю",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Тренировка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     )
 * )
 */
class StartWarmupPath {}

/**
 * @OA\Post(
 *     path="/api/workout-execution/{userWorkout}/complete-warmup",
 *     summary="Завершить разминку досрочно",
 *     description="Завершает разминку досрочно и автоматически переходит к первому упражнению основной тренировки. Тренировка должна быть в статусе 'started'.",
 *     tags={"Workout Execution"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userWorkout",
 *         in="path",
 *         required=true,
 *         description="ID записи тренировки пользователя",
 *         @OA\Schema(type="integer", example=815)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Разминка завершена, переход к основной тренировке",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="type", type="string", example="exercise"),
 *                 @OA\Property(property="user_workout_id", type="integer", example=815),
 *                 @OA\Property(property="needs_weight_input", type="boolean", example=true),
 *                 @OA\Property(
 *                     property="exercise",
 *                     ref="#/components/schemas/ExerciseItem"
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
 *         response=403,
 *         description="Тренировка не принадлежит пользователю",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Тренировка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Тренировка не в статусе started",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="string", example="conflict"),
 *             @OA\Property(property="message", type="string", example="Тренировка не в статусе started")
 *         )
 *     )
 * )
 */
class CompleteWarmupPath {}
