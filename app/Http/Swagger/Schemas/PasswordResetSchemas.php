<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="ForgotPasswordRequest",
 *     type="object",
 *     required={"email"},
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com")
 * )
 */
class PasswordResetSchemas {}

/**
 * @OA\Schema(
 *     schema="ForgotPasswordResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Код для сброса пароля отправлен на вашу почту.")
 * )
 */
class ForgotPasswordResponseSchema {}

/**
 * @OA\Schema(
 *     schema="VerifyResetCodeRequest",
 *     type="object",
 *     required={"email", "code"},
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="code", type="string", minLength=6, maxLength=6, pattern="^[0-9]+$", example="123456")
 * )
 */
class VerifyResetCodeRequestSchema {}

/**
 * @OA\Schema(
 *     schema="VerifyResetCodeResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Код сброса пароля успешно подтвержден.")
 * )
 */
class VerifyResetCodeResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ResetPasswordRequest",
 *     type="object",
 *     required={"email", "code", "password", "password_confirmation"},
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="code", type="string", minLength=6, maxLength=6, pattern="^[0-9]+$", example="123456"),
 *     @OA\Property(property="password", type="string", format="password", minLength=8, maxLength=12, example="newpassword123"),
 *     @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123")
 * )
 */
class ResetPasswordRequestSchema {}

/**
 * @OA\Schema(
 *     schema="ResetPasswordResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Пароль успешно сброшен.")
 * )
 */
class ResetPasswordResponseSchema {}
