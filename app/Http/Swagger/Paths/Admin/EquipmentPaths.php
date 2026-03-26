<?php

namespace App\Http\Swagger\Paths\Admin;

/**
 * @OA\Get(
 *     path="/api/admin/equipments",
 *     summary="Получить список всего оборудования",
 *     description="Возвращает список всего оборудования для использования в формах создания и редактирования упражнений",
 *     operationId="getEquipmentList",
 *     tags={"Admin Equipment"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Equipment")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     )
 * )
 */
class EquipmentPaths {}
