<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Post(
 *     path="/api/verify-email",
 *     summary="Подтверждение email с помощью кода",
 *     tags={"Email Verification"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/VerifyEmailRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Email успешно подтвержден",
 *         @OA\JsonContent(ref="#/components/schemas/VerifyEmailResponse")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Email уже подтвержден или неверный код",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(ref="#/components/schemas/ConflictResponse"),
 *                 @OA\Schema(ref="#/components/schemas/ErrorResponse")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class EmailVerificationPaths {}

/**
 * @OA\Post(
 *     path="/api/resend-verification-code",
 *     summary="Повторная отправка кода подтверждения",
 *     tags={"Email Verification"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ResendVerificationRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Новый код успешно отправлен",
 *         @OA\JsonContent(ref="#/components/schemas/ResendVerificationResponse")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Email уже подтвержден",
 *         @OA\JsonContent(ref="#/components/schemas/ConflictResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Пользователь не найден",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=429,
 *         description="Слишком много попыток",
 *         @OA\JsonContent(ref="#/components/schemas/RateLimitedResponse")
 *     )
 * )
 */
class ResendVerificationPath {}
