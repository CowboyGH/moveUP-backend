<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Post(
 *     path="/api/exercise/reaction",
 *     summary="Оценить выполненное упражнение",
 *     description="Сохраняет оценку пользователя за упражнение и автоматически корректирует нагрузку",
 *     tags={"Exercise Reactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ReactToExerciseRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Оценка успешно сохранена",
 *         @OA\JsonContent(ref="#/components/schemas/ReactToExerciseResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
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
 *         description="Уже оценивали это упражнение сегодня",
 *         @OA\JsonContent(ref="#/components/schemas/ConflictResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class ExerciseReactionPaths {}

/**
 * @OA\Get(
 *     path="/api/exercise/{exerciseId}/reactions/history",
 *     summary="Получить историю оценок для упражнения",
 *     description="Возвращает все оценки пользователя для конкретного упражнения",
 *     tags={"Exercise Reactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="exerciseId",
 *         in="path",
 *         required=true,
 *         description="ID упражнения",
 *         @OA\Schema(type="integer", example=999)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="История оценок",
 *         @OA\JsonContent(ref="#/components/schemas/ReactionHistoryResponse")
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
 *     )
 * )
 */
class ReactionHistoryPath {}

/**
 * @OA\Get(
 *     path="/api/exercise/reactions/statistics",
 *     summary="Получить статистику по всем оценкам",
 *     description="Возвращает сводную статистику по всем оценкам пользователя",
 *     tags={"Exercise Reactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Статистика оценок",
 *         @OA\JsonContent(ref="#/components/schemas/ReactionStatisticsResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     )
 * )
 */
class ReactionStatisticsPath {}

/**
 * @OA\Post(
 *     path="/api/exercise/load-recommendation",
 *     summary="Получить рекомендацию по нагрузке",
 *     description="Возвращает рекомендуемые параметры нагрузки на основе истории оценок",
 *     tags={"Exercise Reactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/GetLoadRecommendationRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Рекомендация по нагрузке",
 *         @OA\JsonContent(ref="#/components/schemas/GetLoadRecommendationResponse")
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
class LoadRecommendationPath {}

/**
 * @OA\Post(
 *     path="/api/workouts/complete-with-adjustments",
 *     summary="Завершить тренировку с корректировкой нагрузки",
 *     description="Завершает тренировку, обрабатывает все оценки и обновляет веса упражнений",
 *     tags={"Workouts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"workout_id", "reactions"},
 *             @OA\Property(property="workout_id", type="integer", example=999),
 *             @OA\Property(
 *                 property="reactions",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"exercise_id", "reaction"},
 *                     @OA\Property(property="exercise_id", type="integer", example=999),
 *                     @OA\Property(property="reaction", type="string", enum={"good", "normal", "bad"}, example="good"),
 *                     @OA\Property(
 *                         property="performance",
 *                         type="object",
 *                         @OA\Property(property="sets_completed", type="integer", example=3),
 *                         @OA\Property(property="reps_completed", type="integer", example=12),
 *                         @OA\Property(property="weight_used", type="number", format="float", example=50.0)
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Тренировка успешно завершена",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Тренировка успешно завершена. Нагрузка скорректирована для следующих тренировок."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="user_workout",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=121),
 *                     @OA\Property(property="completed_at", type="string", format="date-time")
 *                 ),
 *                 @OA\Property(property="exercises_processed", type="integer", example=3),
 *                 @OA\Property(
 *                     property="adjustments_summary",
 *                     type="object",
 *                     @OA\Property(property="increases", type="integer", example=2),
 *                     @OA\Property(property="decreases", type="integer", example=1),
 *                     @OA\Property(property="rest_phases", type="integer", example=0)
 *                 ),
 *                 @OA\Property(
 *                     property="exercise_results",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="exercise_id", type="integer", example=999),
 *                         @OA\Property(property="reaction", type="string", example="good"),
 *                         @OA\Property(property="adjustment_applied", type="boolean", example=true),
 *                         @OA\Property(property="adjustment_type", type="string", example="increase"),
 *                         @OA\Property(property="old_weight", type="number", format="float", example=50.0),
 *                         @OA\Property(property="new_weight", type="number", format="float", example=55.0)
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="updated_weights",
 *                     type="array",
 *                     @OA\Items(ref="#/components/schemas/UserExerciseWeight")
 *                 ),
 *                 @OA\Property(
 *                     property="next_workout_recommendations",
 *                     type="array",
 *                     @OA\Items(type="string", example="Увеличен вес на 2 упражнениях")
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
 *         response=404,
 *         description="Активная тренировка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class CompleteWorkoutWithAdjustmentsPath {}
