<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="VerifyEmailRequest",
 *     type="object",
 *     required={"email", "code"},
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="code", type="string", minLength=6, maxLength=6, pattern="^[0-9]+$", example="123456")
 * )
 */
class EmailVerificationSchemas {}

/**
 * @OA\Schema(
 *     schema="VerifiedUser",
 *     type="object",
 *     title="Подтвержденный пользователь",
 *     @OA\Property(property="id", type="integer", example=335),
 *     @OA\Property(property="role_id", type="integer", example=2),
 *     @OA\Property(property="name", type="string", example="Иван Иванов"),
 *     @OA\Property(property="email", type="string", example="dlololozkin@gmail.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", example="2026-02-13T11:06:26.000000Z"),
 *     @OA\Property(property="avatar", type="string", nullable=true, example=null),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-13T11:05:31.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-13T11:06:26.000000Z")
 * )
 */
class VerifiedUserSchema {}

/**
 * @OA\Schema(
 *     schema="VerifyEmailResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Email успешно подтвержден."),
 *     @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL3ZlcmlmeS1lbWFpbCIsImlhdCI6MTc3MDk4MDc4NywiZXhwIjoxNzcwOTg0Mzg3LCJuYmYiOjE3NzA5ODA3ODcsImp0aSI6ImJiaHZKRmtveFk3eFljOTUiLCJzdWIiOiIzMzUiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3Iiwicm9sZV9pZCI6MiwiZW1haWwiOiJkbG9sb2xvemtpbkBnbWFpbC5jb20iLCJuYW1lIjoi0JjQstCw0L0g0JjQstCw0L3QvtCyIiwiYXZhdGFyIjpudWxsLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZX0.rIEZ84LF6GIWw8yDwN3hQKnwhoyhfDRDUoKTXk3Cz5I"),
 *     @OA\Property(property="token_type", type="string", example="bearer"),
 *     @OA\Property(property="expires_in", type="integer", example=3600),
 *     @OA\Property(property="user", ref="#/components/schemas/VerifiedUser")
 * )
 */
class VerifyEmailResponseSchema {}

/**
 * @OA\Schema(
 *     schema="ResendVerificationRequest",
 *     type="object",
 *     required={"email"},
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com")
 * )
 */
class ResendVerificationRequestSchema {}

/**
 * @OA\Schema(
 *     schema="ResendVerificationResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Новый код подтверждения отправлен на вашу почту.")
 * )
 */
class ResendVerificationResponseSchema {}
