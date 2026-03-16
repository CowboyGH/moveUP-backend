<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="GuestTestStartResponse",
 *     type="object",
 *     title="Ответ на начало теста для гостя",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Тест начат для гостя"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="attempt_id", type="string", example="guest_816a590b-e438-49d8-962e-678e80849e58"),
 *         @OA\Property(
 *             property="testing",
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=8),
 *             @OA\Property(property="title", type="string", example="id consectetur aperiam autem"),
 *             @OA\Property(property="description", type="string", example="Ut laboriosam ipsa aut voluptatum. Quisquam repellat voluptates exercitationem quia."),
 *             @OA\Property(property="duration_minutes", type="string", example="31"),
 *             @OA\Property(property="image", type="string", example="tests/"),
 *             @OA\Property(property="total_exercises", type="integer", example=4)
 *         ),
 *         @OA\Property(
 *             property="current_exercise",
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=3),
 *             @OA\Property(property="description", type="string", example="Velit reiciendis dolores magni mollitia molestias libero."),
 *             @OA\Property(property="image", type="string", example="testing_exercises/"),
 *             @OA\Property(property="order_number", type="integer", example=1)
 *         )
 *     )
 * )
 */
class GuestTestSchemas {}

/**
 * @OA\Schema(
 *     schema="GuestTestStoreResultResponse",
 *     type="object",
 *     title="Ответ на сохранение результата упражнения",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Результат сохранён для гостя"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="saved", type="boolean", example=true),
 *         @OA\Property(
 *             property="result",
 *             type="object",
 *             @OA\Property(property="testing_exercise_id", type="integer", example=16),
 *             @OA\Property(property="result_value", type="integer", example=2)
 *         ),
 *         @OA\Property(
 *             property="next_exercise",
 *             type="object",
 *             nullable=true,
 *             description="Следующее упражнение (если есть)",
 *             @OA\Property(property="id", type="integer", example=4),
 *             @OA\Property(property="description", type="string", example="Приседания за 1 минуту"),
 *             @OA\Property(property="image", type="string", example="testing_exercises/"),
 *             @OA\Property(property="order_number", type="integer", example=2)
 *         ),
 *         @OA\Property(property="all_exercises_completed", type="boolean", nullable=true, example=true),
 *         @OA\Property(property="message", type="string", nullable=true, example="Все упражнения выполнены. Введите пульс для завершения теста.")
 *     )
 * )
 */
class GuestTestStoreResultResponse {}

/**
 * @OA\Schema(
 *     schema="GuestTestCompleteResponse",
 *     type="object",
 *     title="Ответ на завершение теста",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Тест успешно завершён для гостя"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="attempt_id", type="string", example="guest_816a590b-e438-49d8-962e-678e80849e58"),
 *         @OA\Property(property="completed_at", type="string", format="datetime", example="2026-03-13 14:30:11"),
 *         @OA\Property(property="pulse", type="integer", example=151)
 *     )
 * )
 */
class GuestTestCompleteResponse {}

/**
 * @OA\Schema(
 *     schema="GuestTestHistoryResponse",
 *     type="object",
 *     title="Ответ с историей тестов гостя",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="История тестов гостя"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="statistics",
 *             type="object",
 *             @OA\Property(property="total_attempts", type="integer", example=1),
 *             @OA\Property(property="completed_attempts", type="integer", example=1),
 *             @OA\Property(property="last_test_date", type="string", format="datetime", example="2026-03-13 14:17:50")
 *         ),
 *         @OA\Property(
 *             property="history",
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="attempt_id", type="string", example="guest_7dc2bd47-8f9d-4da9-b67d-a1c02127cb75"),
 *                 @OA\Property(property="testing_id", type="integer", example=8),
 *                 @OA\Property(property="testing_title", type="string", example="id consectetur aperiam autem"),
 *                 @OA\Property(property="completed_at", type="string", format="datetime", example="2026-03-13 14:17:50"),
 *                 @OA\Property(property="pulse", type="integer", example=150),
 *                 @OA\Property(property="exercises_count", type="integer", example=4)
 *             )
 *         )
 *     )
 * )
 */
class GuestTestHistoryResponse {}

/**
 * @OA\Schema(
 *     schema="GuestTestResetResponse",
 *     type="object",
 *     title="Ответ на сброс тестов",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Результаты тестов гостя сброшены")
 * )
 */
class GuestTestResetResponse {}
