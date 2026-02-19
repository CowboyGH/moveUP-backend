<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="SavedCard",
 *     type="object",
 *     title="Сохраненная карта",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="card_holder", type="string", example="IVAN IVANOV"),
 *     @OA\Property(property="card_last_four", type="string", example="1111"),
 *     @OA\Property(property="expiry_month", type="string", example="12"),
 *     @OA\Property(property="expiry_year", type="string", example="2025"),
 *     @OA\Property(property="expiry_formatted", type="string", example="12/2025"),
 *     @OA\Property(property="is_default", type="boolean", example=true)
 * )
 */
class PaymentSchemas {}

/**
 * @OA\Schema(
 *     schema="Subscription",
 *     type="object",
 *     title="Подписка",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="1 month"),
 *     @OA\Property(property="price", type="string", example="9.99")
 * )
 */
class SubscriptionSchema {}

/**
 * @OA\Schema(
 *     schema="UserSubscription",
 *     type="object",
 *     title="Подписка пользователя",
 *     @OA\Property(property="id", type="integer", example=25),
 *     @OA\Property(property="start_date", type="string", format="date", example="2026-02-19"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2026-03-21"),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 */
class UserSubscriptionSchema {}

/**
 * @OA\Schema(
 *     schema="PaymentResult",
 *     type="object",
 *     title="Результат платежа",
 *     @OA\Property(property="transaction_id", type="string", example="pay_69967c1f59273_1771469855"),
 *     @OA\Property(property="subscription", ref="#/components/schemas/Subscription"),
 *     @OA\Property(property="user_subscription", ref="#/components/schemas/UserSubscription"),
 *     @OA\Property(property="card_saved", type="boolean", example=true),
 *     @OA\Property(
 *         property="saved_card",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=7),
 *         @OA\Property(property="last_four", type="string", example="1111"),
 *         @OA\Property(property="is_default", type="boolean", example=false)
 *     )
 * )
 */
class PaymentResultSchema {}

/**
 * @OA\Schema(
 *     schema="CardsListResponse",
 *     type="object",
 *     title="Список сохраненных карт",
 *     @OA\Property(property="status", type="string", example="success"),
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/SavedCard")
 *     )
 * )
 */
class CardsListResponseSchema {}
