<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="SaveCardRequest",
 *     type="object",
 *     title="Запрос на сохранение карты",
 *     required={"card_number", "card_holder", "expiry_month", "expiry_year"},
 *     @OA\Property(
 *         property="card_number",
 *         type="string",
 *         example="4111111111117777",
 *         description="16 цифр номера карты",
 *         minLength=16,
 *         maxLength=16
 *     ),
 *     @OA\Property(
 *         property="card_holder",
 *         type="string",
 *         example="IVAN IVANOV",
 *         description="Имя держателя карты латиницей"
 *     ),
 *     @OA\Property(
 *         property="expiry_month",
 *         type="string",
 *         example="02",
 *         description="Месяц окончания (2 цифры)",
 *         minLength=2,
 *         maxLength=2
 *     ),
 *     @OA\Property(
 *         property="expiry_year",
 *         type="string",
 *         example="2029",
 *         description="Год окончания (4 цифры)",
 *         minLength=4,
 *         maxLength=4
 *     )
 * )
 */
class PaymentRequestSchemas {}

/**
 * @OA\Schema(
 *     schema="SubscriptionPaymentRequest",
 *     type="object",
 *     title="Запрос на оплату подписки",
 *     required={"subscription_id", "save_card", "use_saved_card"},
 *     @OA\Property(property="subscription_id", type="integer", example=1),
 *     @OA\Property(property="save_card", type="boolean", example=true),
 *     @OA\Property(property="use_saved_card", type="boolean", example=false),
 *     @OA\Property(property="saved_card_id", type="integer", example=1, description="Обязателен если use_saved_card=true"),
 *     @OA\Property(property="card_number", type="string", example="4111111111111111", description="Обязателен если use_saved_card=false"),
 *     @OA\Property(property="card_holder", type="string", example="IVAN IVANOV", description="Обязателен если use_saved_card=false"),
 *     @OA\Property(property="expiry_month", type="string", example="12", description="Обязателен если use_saved_card=false"),
 *     @OA\Property(property="expiry_year", type="string", example="2025", description="Обязателен если use_saved_card=false"),
 *     @OA\Property(property="cvv", type="string", example="123", description="Обязателен если use_saved_card=false")
 * )
 */
class SubscriptionPaymentRequestSchema {}

/**
 * @OA\Schema(
 *     schema="PaymentResponse",
 *     type="object",
 *     allOf={
 *         @OA\Schema(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Подписка успешно оформлена")
 *         ),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/PaymentResult"
 *             )
 *         )
 *     }
 * )
 */
class PaymentResponseSchema {}
