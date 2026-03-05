<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="ImageNotFoundResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *         @OA\Schema(
 *             @OA\Property(property="code", type="string", example="not_found"),
 *             @OA\Property(property="message", type="string", example="Изображение не найдено")
 *         )
 *     }
 * )
 */
class ImageSchemas {}
