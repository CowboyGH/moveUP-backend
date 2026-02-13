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
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
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
 *     description="Отправляет новый 6-значный код подтверждения на email пользователя",
 *     tags={"Email Verification"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ResendVerificationRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Новый код успешно отправлен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Новый код подтверждения отправлен на вашу почту.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Email уже подтвержден",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Email уже подтвержден.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Пользователь не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Пользователь с таким email не найден.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=429,
 *         description="Слишком много попыток",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Слишком много попыток. Попробуйте позже.")
 *         )
 *     )
 * )
 */
class ResendVerificationPath {}
