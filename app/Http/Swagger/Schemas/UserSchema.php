<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="Пользователь",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Иван Иванов"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="role_id", type="integer", example=1),
 *     @OA\Property(property="avatar", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class UserSchema
{
    // Схема пользователя
}

/**
 * @OA\Schema(
 *     schema="AuthUser",
 *     type="object",
 *     title="Пользователь для авторизации",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Иван Иванов"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time")
 * )
 */
class AuthUserSchema
{
    // Схема пользователя для ответа авторизации
}

/**
 * @OA\Schema(
 *     schema="RegisteredUser",
 *     type="object",
 *     title="Зарегистрированный пользователь",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Иван Иванов"),
 *     @OA\Property(property="email", type="string", example="user@example.com"),
 *     @OA\Property(property="role_id", type="integer", example=1),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class RegisteredUserSchema
{
    // Схема для регистрации
}
