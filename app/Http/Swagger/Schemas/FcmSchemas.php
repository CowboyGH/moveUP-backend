<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="FcmTokenRequest",
 *     type="object",
 *     title="Запрос на сохранение FCM токена",
 *     required={"fcm_token"},
 *     @OA\Property(
 *         property="fcm_token",
 *         type="string",
 *         description="FCM токен от устройства",
 *         example="cP6Ks-XyZ123...abc"
 *     ),
 *     @OA\Property(
 *         property="device_type",
 *         type="string",
 *         enum={"ios", "android", "web"},
 *         description="Тип устройства",
 *         example="android"
 *     ),
 *     @OA\Property(
 *         property="device_name",
 *         type="string",
 *         description="Название устройства",
 *         example="Xiaomi Redmi Note 10"
 *     )
 * )
 */
class FcmSchemas {}

/**
 * @OA\Schema(
 *     schema="FcmTokenResponse",
 *     type="object",
 *     title="Ответ на сохранение FCM токена",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="message", example="FCM токен успешно сохранен")
 *         )
 *     }
 * )
 */
class FcmTokenResponseSchema {}

/**
 * @OA\Schema(
 *     schema="FcmTokenDeleteRequest",
 *     type="object",
 *     title="Запрос на удаление FCM токена",
 *     required={"fcm_token"},
 *     @OA\Property(
 *         property="fcm_token",
 *         type="string",
 *         description="FCM токен для удаления",
 *         example="cP6Ks-XyZ123...abc"
 *     )
 * )
 */
class FcmTokenDeleteRequestSchema {}

/**
 * @OA\Schema(
 *     schema="FcmTokenDeleteResponse",
 *     type="object",
 *     title="Ответ на удаление FCM токена",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="message", example="FCM токен удален")
 *         )
 *     }
 * )
 */
class FcmTokenDeleteResponseSchema {}
