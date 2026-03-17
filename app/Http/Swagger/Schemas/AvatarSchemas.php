<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="UploadAvatarResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Аватар успешно загружен"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="avatar_url", type="string", example="http://localhost/storage/avatars/avatar_1_1234567890.jpg"),
 *         @OA\Property(property="avatar_path", type="string", example="avatars/avatar_1_1234567890.jpg")
 *     )
 * )
 */
class AvatarSchemas {}

/**
 * @OA\Schema(
 *     schema="DeleteAvatarResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Аватар удален"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="avatar_url", type="null", example=null)
 *     )
 * )
 */
class DeleteAvatarResponseSchema {}

/**
 * @OA\Schema(
 *     schema="AvatarNotFoundResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="code", type="string", example="not_found"),
 *             @OA\Property(property="message", type="string", example="Аватар не найден")
 *         )
 *     }
 * )
 */
class AvatarNotFoundResponseSchema {}
