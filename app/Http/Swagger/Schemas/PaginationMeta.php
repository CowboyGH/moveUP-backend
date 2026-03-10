<?php

namespace App\Http\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     type="object",
 *     title="Метаданные пагинации",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=5),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="total", type="integer", example=75),
 *     @OA\Property(property="from", type="integer", example=1),
 *     @OA\Property(property="to", type="integer", example=15)
 * )
 */
class PaginationMeta {}
