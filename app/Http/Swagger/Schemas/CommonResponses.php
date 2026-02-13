<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     title="Успешный ответ",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Операция выполнена успешно")
 * )
 */
class CommonResponses {}

/**
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="Ответ с ошибкой",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Сообщение об ошибке")
 * )
 */
class ErrorResponseSchema {}

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
 *             @OA\Items(type="string", example="Поле обязательно для заполнения")
 *         ),
 *         example={
 *             "email": {"Поле email обязательно для заполнения"},
 *             "password": {"Пароль должен содержать минимум 8 символов"}
 *         }
 *     )
 * )
 */
class ValidationErrorResponseSchema {}
