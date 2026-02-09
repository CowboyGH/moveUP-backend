<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="Ответ с ошибкой",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Сообщение об ошибке")
 * )
 */
class ErrorSchema
{
    // Схема ответа с ошибкой
}

/**
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     title="Ответ с ошибками валидации",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\AdditionalProperties(
 *             type="array",
 *             @OA\Items(type="string")
 *         )
 *     )
 * )
 */
class ValidationErrorResponseSchema
{
    // Схема ошибки валидации
}

/**
 * @OA\Schema(
 *     schema="AuthResponse",
 *     type="object",
 *     title="Ответ авторизации",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
 *     @OA\Property(property="token_type", type="string", example="bearer"),
 *     @OA\Property(property="expires_in", type="integer", example=3600),
 *     @OA\Property(property="user", ref="#/components/schemas/AuthUser")
 * )
 */
class AuthResponseSchema
{
    // Схема ответа авторизации
}
