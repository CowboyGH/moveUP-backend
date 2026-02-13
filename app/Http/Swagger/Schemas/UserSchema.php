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
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="role_id", type="integer", example=2),
 *     @OA\Property(property="avatar", type="string", nullable=true, example="avatars/user1.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 * )
 */
class UserSchema {}

/**
 * @OA\Schema(
 *     schema="UserWithToken",
 *     type="object",
 *     title="Пользователь с токеном",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/User"),
 *         @OA\Schema(
 *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
 *             @OA\Property(property="token_type", type="string", example="bearer"),
 *             @OA\Property(property="expires_in", type="integer", example=3600)
 *         )
 *     }
 * )
 */
class UserWithTokenSchema {}
