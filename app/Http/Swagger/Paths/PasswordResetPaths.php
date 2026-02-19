<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Post(
 *     path="/api/forgot-password",
 *     summary="Запрос на сброс пароля",
 *     tags={"Password Reset"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ForgotPasswordRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Код сброса отправлен на email",
 *         @OA\JsonContent(ref="#/components/schemas/ForgotPasswordResponse")
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
class PasswordResetPaths {}

/**
 * @OA\Post(
 *     path="/api/verify-reset-code",
 *     summary="Проверка кода сброса пароля",
 *     tags={"Password Reset"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/VerifyResetCodeRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Код успешно подтвержден",
 *         @OA\JsonContent(ref="#/components/schemas/VerifyResetCodeResponse")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Неверный код",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
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
class VerifyResetCodePath {}

/**
 * @OA\Post(
 *     path="/api/reset-password",
 *     summary="Сброс пароля",
 *     tags={"Password Reset"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ResetPasswordRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Пароль успешно сброшен",
 *         @OA\JsonContent(ref="#/components/schemas/ResetPasswordResponse")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Неверный код",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
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
class ResetPasswordPath {}
