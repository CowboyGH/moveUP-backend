<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     title="Успешный ответ",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Операция выполнена успешно"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         description="Опциональные данные ответа",
 *         nullable=true
 *     )
 * )
 */
class CommonResponses {}

/**
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     required={"code", "message"},
 *     @OA\Property(property="code", type="string", description="Код ошибки"),
 *     @OA\Property(property="message", type="string", description="Сообщение об ошибке")
 * )
 */
class ErrorResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     required={"code", "message", "errors"},
 *     @OA\Property(property="code", type="string", example="validation_failed"),
 *     @OA\Property(property="message", type="string", example="Ошибка валидации"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         description="Детальные ошибки валидации",
 *         example={
 *             "email": {"Поле email обязательно"},
 *             "password": {"Пароль должен содержать минимум 8 символов"}
 *         }
 *     )
 * )
 */
class ValidationErrorResponseSchema {}

/**
 * @OA\Schema(
 *     schema="InvalidCredentialsResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="code", example="invalid_credentials"),
 *             @OA\Property(property="message", example="Неверные учетные данные.")
 *         )
 *     }
 * )
 */
class InvalidCredentialsResponseSchema {}

/**
 * @OA\Schema(
 *     schema="EmailNotVerifiedResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="code", example="email_not_verified"),
 *             @OA\Property(property="message", example="Email не подтвержден.")
 *         )
 *     }
 * )
 */
class EmailNotVerifiedResponseSchema {}

/**
 * @OA\Schema(
 *     schema="TokenExpiredResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="code", example="token_expired"),
 *             @OA\Property(property="message", example="Токен истек.")
 *         )
 *     }
 * )
 */
class TokenExpiredResponseSchema {}

/**
 * @OA\Schema(
 *     schema="InactivityErrorResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="code", example="session_expired_inactivity"),
 *             @OA\Property(property="message", example="Сессия завершена после 7 дней бездействия.")
 *         )
 *     }
 * )
 */
class InactivityErrorResponseSchema {}

/**
 * @OA\Schema(
 *     schema="SessionExpiredAbsoluteResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="code", example="session_expired_absolute"),
 *             @OA\Property(property="message", example="Срок действия сессии истек. Войдите снова.")
 *         )
 *     }
 * )
 */
class SessionExpiredAbsoluteResponseSchema {}

/**
 * @OA\Schema(
 *     schema="UnauthorizedResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="code", example="unauthorized"),
 *             @OA\Property(property="message", example="Неавторизован.")
 *         )
 *     }
 * )
 */
class UnauthorizedResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ForbiddenResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="code", example="forbidden"),
 *             @OA\Property(property="message", example="Доступ запрещен.")
 *         )
 *     }
 * )
 */
class ForbiddenResponseSchema {}

/**
 * @OA\Schema(
 *     schema="NotFoundResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="code", example="not_found"),
 *             @OA\Property(property="message", example="Ресурс не найден.")
 *         )
 *     }
 * )
 */
class NotFoundResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ConflictResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="code", example="conflict"),
 *             @OA\Property(property="message", example="Конфликт данных.")
 *         )
 *     }
 * )
 */
class ConflictResponseSchema {}

/**
 * @OA\Schema(
 *     schema="RateLimitedResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="code", example="rate_limited"),
 *             @OA\Property(property="message", example="Слишком много запросов. Попробуйте позже.")
 *         )
 *     }
 * )
 */
class RateLimitedResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ServerErrorResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="code", example="server_error"),
 *             @OA\Property(property="message", example="Внутренняя ошибка сервера.")
 *         )
 *     }
 * )
 */
class ServerErrorResponseSchema {}
