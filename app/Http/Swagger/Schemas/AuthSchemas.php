<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     type="object",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string", maxLength=20, example="Иван Иванов", description="Только буквы и пробелы"),
 *     @OA\Property(property="email", type="string", format="email", maxLength=255, example="user@example.com"),
 *     @OA\Property(property="password", type="string", format="password", minLength=8, maxLength=12, example="password123", description="Только латинские буквы и цифры")
 * )
 */
class AuthSchemas {}

/**
 * @OA\Schema(
 *     schema="RegisterResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Регистрация прошла успешно. Проверьте вашу почту для получения кода подтверждения."),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="name", type="string", example="Иван Иванов"),
 *         @OA\Property(property="email", type="string", example="user@example.com"),
 *         @OA\Property(property="role_id", type="integer", example=2),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-13T10:57:14.000000Z"),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-13T10:57:14.000000Z"),
 *         @OA\Property(property="id", type="integer", example=333)
 *     )
 * )
 */
class RegisterResponseSchema {}

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123")
 * )
 */
class LoginRequestSchema {}

/**
 * @OA\Schema(
 *     schema="SessionConfig",
 *     description="Конфигурация сессии",
 *     properties={
 *         @OA\Property(property="lifetime_days", type="integer", example=30, description="Общая длительность сессии в днях"),
 *         @OA\Property(property="inactivity_limit_days", type="integer", example=7, description="Лимит неактивности в днях"),
 *         @OA\Property(property="access_token_expires_in_minutes", type="integer", example=60, description="Время жизни access token в минутах")
 *     },
 *     type="object"
 * )
 */
class SessionConfigSchema {}

/**
 * @OA\Schema(
 *     schema="LoginResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
 *     @OA\Property(property="token_type", type="string", example="bearer"),
 *     @OA\Property(property="expires_in", type="integer", example=3600, description="Время жизни access token в секундах"),
 *     @OA\Property(property="refresh_expires_in", type="integer", example=604800, description="Время жизни refresh token в секундах (7 дней)"),
 *     @OA\Property(
 *         property="session",
 *         ref="#/components/schemas/SessionConfig"
 *     ),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="role_id", type="integer", example=2),
 *         @OA\Property(property="name", type="string", example="Иван Иванов"),
 *         @OA\Property(property="email", type="string", example="user@example.com"),
 *         @OA\Property(property="email_verified_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *         @OA\Property(property="avatar", type="string", nullable=true, example="avatars/user1.jpg"),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 *     )
 * )
 */
class LoginResponseSchema {}

/**
 * @OA\Schema(
 *     schema="LogoutResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Успешный выход из системы.")
 * )
 */
class LogoutResponseSchema {}

/**
 * @OA\Schema(
 *     schema="RefreshResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
 *     @OA\Property(property="token_type", type="string", example="bearer"),
 *     @OA\Property(property="expires_in", type="integer", example=3600)
 * )
 */
class RefreshResponseSchema {}

/**
 * @OA\Schema(
 *     schema="InactivityErrorResponse",
 *     properties={
 *         @OA\Property(property="success", type="boolean", example=false),
 *         @OA\Property(property="message", type="string", example="Сессия завершена после 7 дней бездействия."),
 *         @OA\Property(property="code", type="string", example="session_expired_inactivity")
 *     },
 *     type="object"
 * )
 */
class InactivityErrorResponseSchema {}

/**
 * @OA\Schema(
 *     schema="MeResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="role_id", type="integer", example=2),
 *         @OA\Property(property="name", type="string", example="Иван Иванов"),
 *         @OA\Property(property="email", type="string", example="user@example.com"),
 *         @OA\Property(property="email_verified_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *         @OA\Property(property="avatar", type="string", nullable=true, example="avatars/user1.jpg"),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 *     )
 * )
 */
class MeResponseSchema {}
