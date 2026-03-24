<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="ExerciseReaction",
 *     type="object",
 *     title="Оценка упражнения",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="exercise_id", type="integer", example=999),
 *     @OA\Property(property="user_workout_id", type="integer", example=121),
 *     @OA\Property(property="reaction", type="string", enum={"good", "normal", "bad"}, example="good"),
 *     @OA\Property(property="reaction_date", type="string", format="date", example="2026-03-04"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class ExerciseReactionSchemas {}

/**
 * @OA\Schema(
 *     schema="ReactionAnalysis",
 *     type="object",
 *     title="Анализ оценок",
 *     @OA\Property(property="pattern", type="string", enum={"no_data", "consistently_good", "consistently_bad", "mostly_good", "mostly_bad", "mixed"}, example="consistently_good"),
 *     @OA\Property(property="consecutive_good", type="integer", example=2),
 *     @OA\Property(property="consecutive_bad", type="integer", example=0),
 *     @OA\Property(property="last_reaction", type="string", enum={"good", "normal", "bad"}, example="good"),
 *     @OA\Property(property="trend", type="string", enum={"neutral", "positive_streak", "negative", "negative_critical"}, example="positive_streak"),
 *     @OA\Property(
 *         property="stats",
 *         type="object",
 *         @OA\Property(property="good", type="integer", example=3),
 *         @OA\Property(property="normal", type="integer", example=1),
 *         @OA\Property(property="bad", type="integer", example=0),
 *         @OA\Property(property="total", type="integer", example=4)
 *     )
 * )
 */
class ReactionAnalysisSchema {}

/**
 * @OA\Schema(
 *     schema="LoadAdjustment",
 *     type="object",
 *     title="Корректировка нагрузки",
 *     @OA\Property(property="applied", type="boolean", example=true),
 *     @OA\Property(property="type", type="string", enum={"increase", "decrease"}, example="increase"),
 *     @OA\Property(property="percent", type="integer", example=10),
 *     @OA\Property(property="old_weight", type="number", format="float", example=50.0),
 *     @OA\Property(property="new_weight", type="number", format="float", example=55.0),
 *     @OA\Property(property="message", type="string", example="Увеличьте вес с 50кг до 55кг")
 * )
 */
class LoadAdjustmentSchema {}

/**
 * @OA\Schema(
 *     schema="RestPhase",
 *     type="object",
 *     title="Фаза отдыха",
 *     @OA\Property(property="required", type="boolean", example=true),
 *     @OA\Property(property="duration_days", type="integer", example=3),
 *     @OA\Property(property="message", type="string", example="Рекомендуется фаза отдыха от этого упражнения на 3 дня")
 * )
 */
class RestPhaseSchema {}

/**
 * @OA\Schema(
 *     schema="ExerciseLoad",
 *     type="object",
 *     title="Параметры нагрузки упражнения",
 *     @OA\Property(property="weight", type="number", format="float", example=50.0),
 *     @OA\Property(property="sets", type="integer", example=3),
 *     @OA\Property(property="reps", type="integer", example=12),
 *     @OA\Property(property="difficulty", type="string", enum={"beginner", "medium", "advanced", "expert"}, example="medium")
 * )
 */
class ExerciseLoadSchema {}

/**
 * @OA\Schema(
 *     schema="ReactToExerciseRequest",
 *     type="object",
 *     required={"user_workout_id", "exercise_id", "reaction"},
 *     @OA\Property(property="user_workout_id", type="integer", example=121, description="ID активной тренировки"),
 *     @OA\Property(property="exercise_id", type="integer", example=999, description="ID упражнения"),
 *     @OA\Property(property="reaction", type="string", enum={"good", "normal", "bad"}, example="good", description="Оценка выполнения"),
 *     @OA\Property(property="sets_completed", type="integer", example=3, description="Выполнено подходов"),
 *     @OA\Property(property="reps_completed", type="integer", example=12, description="Выполнено повторений"),
 *     @OA\Property(property="weight_used", type="number", format="float", example=50.0, description="Использованный вес (кг)")
 * )
 */
class ReactToExerciseRequestSchema {}

/**
 * @OA\Schema(
 *     schema="ReactToExerciseResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Оценка упражнения сохранена"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="reaction", ref="#/components/schemas/ExerciseReaction"),
 *         @OA\Property(property="analysis", ref="#/components/schemas/ReactionAnalysis"),
 *         @OA\Property(property="adjustments", ref="#/components/schemas/LoadAdjustment"),
 *         @OA\Property(property="rest_phase", ref="#/components/schemas/RestPhase", nullable=true),
 *         @OA\Property(property="current_weight", type="string", example=50.0),
 *         @OA\Property(property="recommendations", type="array", @OA\Items(type="string", example="Увеличьте вес с 50кг до 55кг"))
 *     )
 * )
 */
class ReactToExerciseResponseSchema {}

/**
 * @OA\Schema(
 *     schema="GetLoadRecommendationRequest",
 *     type="object",
 *     required={"exercise_id"},
 *     @OA\Property(property="exercise_id", type="integer", example=999, description="ID упражнения")
 * )
 */
class GetLoadRecommendationRequestSchema {}

/**
 * @OA\Schema(
 *     schema="GetLoadRecommendationResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="success"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="exercise_id", type="integer", example=999),
 *         @OA\Property(property="current_load", ref="#/components/schemas/ExerciseLoad"),
 *         @OA\Property(property="recommended_load", ref="#/components/schemas/ExerciseLoad"),
 *         @OA\Property(property="explanation", type="string", example="Рекомендуется увеличить вес на 10%"),
 *         @OA\Property(property="analysis", ref="#/components/schemas/ReactionAnalysis"),
 *         @OA\Property(property="rest_phase_needed", type="boolean", example=false)
 *     )
 * )
 */
class GetLoadRecommendationResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ReactionHistoryResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="success"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="history",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/ExerciseReaction")
 *         ),
 *         @OA\Property(property="analysis", ref="#/components/schemas/ReactionAnalysis")
 *     )
 * )
 */
class ReactionHistoryResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ReactionStatisticsResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="success"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="summary",
 *             type="object",
 *             @OA\Property(property="total_reactions", type="integer", example=10),
 *             @OA\Property(property="good_percentage", type="integer", example=70),
 *             @OA\Property(property="normal_percentage", type="integer", example=20),
 *             @OA\Property(property="bad_percentage", type="integer", example=10),
 *             @OA\Property(property="exercises_with_reactions", type="integer", example=3)
 *         ),
 *         @OA\Property(
 *             property="exercises",
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="exercise_id", type="integer", example=999),
 *                 @OA\Property(property="exercise_name", type="string", example="Приседания"),
 *                 @OA\Property(property="total_reactions", type="integer", example=5),
 *                 @OA\Property(property="last_reaction", type="string", enum={"good", "normal", "bad"}, example="good"),
 *                 @OA\Property(property="last_reaction_date", type="string", format="date", example="2026-03-04"),
 *                 @OA\Property(property="analysis", ref="#/components/schemas/ReactionAnalysis")
 *             )
 *         )
 *     )
 * )
 */
class ReactionStatisticsResponseSchema {}

/**
 * @OA\Schema(
 *     schema="UserExerciseWeight",
 *     type="object",
 *     @OA\Property(property="exercise_id", type="integer", example=999),
 *     @OA\Property(property="exercise_name", type="string", example="Приседания"),
 *     @OA\Property(property="current_weight", type="string", example=55.0),
 *     @OA\Property(property="adjustment_factor", type="number", format="float", example=1.1)
 * )
 */
class UserExerciseWeightSchema {}
