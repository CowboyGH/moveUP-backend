<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="VerifyEmailRequest",
 *     type="object",
 *     required={"email", "code"},
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="code", type="string", minLength=6, maxLength=6, pattern="^[0-9]+$", example="123456")
 * )
 * @OA\Schema(
 *     schema="VerifiedUser",
 *     type="object",
 *     title="Подтвержденный пользователь",
 *     @OA\Property(property="id", type="integer", example=292),
 *     @OA\Property(property="role_id", type="integer", example=2),
 *     @OA\Property(property="name", type="string", example="Иван Иванов"),
 *     @OA\Property(property="email", type="string", example="user@example.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", example="2026-03-09T14:50:56.000000Z"),
 *     @OA\Property(property="avatar", type="string", nullable=true, example=null),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-09T14:49:53.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-03-09T14:50:56.000000Z"),
 *     @OA\Property(property="fcm_token", type="string", nullable=true, example=null),
 *     @OA\Property(property="avatar_url", type="string", nullable=true, example=null)
 * )
 * @OA\Schema(
 *     schema="VerifyEmailResponseData",
 *     type="object",
 *     @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
 *     @OA\Property(property="token_type", type="string", example="bearer"),
 *     @OA\Property(property="expires_in", type="integer", example=3600),
 *     @OA\Property(property="user", ref="#/components/schemas/VerifiedUser")
 * )
 * @OA\Schema(
 *     schema="VerifyEmailResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Email успешно подтвержден."),
 *     @OA\Property(
 *         property="data",
 *         ref="#/components/schemas/VerifyEmailResponseData"
 *     )
 * )
 * @OA\Schema(
 *     schema="ResendVerificationRequest",
 *     type="object",
 *     required={"email"},
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com")
 * )
 * @OA\Schema(
 *     schema="ResendVerificationResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Новый код подтверждения отправлен на вашу почту.")
 * )
 */
class EmailVerificationSchemas {}
