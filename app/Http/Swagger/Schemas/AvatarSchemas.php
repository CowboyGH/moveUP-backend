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
