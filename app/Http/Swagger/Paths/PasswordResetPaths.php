<?php

namespace App\Http\Swagger\Paths;

/**
 * Запрос на восстановление пароля
 *
 * @OA\Post(
 *     path="/api/forgot-password",
 *     summary="Запрос на восстановление пароля",
 *     tags={"Password Reset"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Код отправлен на email",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Код для сброса пароля отправлен на вашу почту.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class PasswordResetPaths {}

/**
 * Подтверждение кода сброса пароля
 *
 * @OA\Post(
 *     path="/api/verify-reset-code",
 *     summary="Подтверждение кода сброса пароля",
 *     tags={"Password Reset"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "code"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="code", type="string", example="123456")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Код подтвержден",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Код сброса пароля успешно подтвержден.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Неверный или истекший код",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class VerifyResetCodePath {}

/**
 * Сброс пароля
 *
 * @OA\Post(
 *     path="/api/reset-password",
 *     summary="Сброс пароля",
 *     tags={"Password Reset"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "code", "password", "password_confirmation"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="code", type="string", example="123456"),
 *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Пароль успешно сброшен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Пароль успешно сброшен.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Неверный или истекший код",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class ResetPasswordPath {}
