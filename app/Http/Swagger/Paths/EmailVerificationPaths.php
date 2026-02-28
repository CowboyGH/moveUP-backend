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
 *         description="Ошибка бизнес-логики",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="code", type="string", example="email_already_verified"),
 *                     @OA\Property(property="message", type="string", example="Email уже подтвержден.")
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="code", type="string", example="validation_failed"),
 *                     @OA\Property(property="message", type="string", example="Неверный или истекший код подтверждения.")
 *                 )
 *             }
 *         )
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
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="string", example="email_already_verified"),
 *             @OA\Property(property="message", type="string", example="Email уже подтвержден.")
 *         )
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
