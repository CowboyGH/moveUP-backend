<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Post(
 *     path="/api/profile/avatar",
 *     summary="Загрузка/обновление аватара",
 *     tags={"Avatar"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"avatar"},
 *                 @OA\Property(
 *                     property="avatar",
 *                     type="string",
 *                     format="binary",
 *                     description="Файл изображения (jpeg, png, jpg, gif, webp, макс. 5MB)"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Аватар успешно загружен",
 *         @OA\JsonContent(ref="#/components/schemas/UploadAvatarResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class AvatarPaths {}

/**
 * @OA\Delete(
 *     path="/api/profile/avatar",
 *     summary="Удаление аватара",
 *     tags={"Avatar"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Аватар успешно удален",
 *         @OA\JsonContent(ref="#/components/schemas/DeleteAvatarResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Аватар не найден",
 *         @OA\JsonContent(ref="#/components/schemas/AvatarNotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class DeleteAvatarPath {}

/**
 * @OA\Get(
 *     path="/api/avatars/{userId}",
 *     summary="Публичное получение аватара пользователя",
 *     description="Возвращает файл изображения аватара пользователя. Не требует авторизации.",
 *     tags={"Avatar"},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         description="ID пользователя",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Изображение аватара",
 *         @OA\MediaType(
 *             mediaType="image/jpeg",
 *             @OA\Schema(type="string", format="binary")
 *         ),
 *         @OA\MediaType(
 *             mediaType="image/png",
 *             @OA\Schema(type="string", format="binary")
 *         ),
 *         @OA\MediaType(
 *             mediaType="image/gif",
 *             @OA\Schema(type="string", format="binary")
 *         ),
 *         @OA\MediaType(
 *             mediaType="image/webp",
 *             @OA\Schema(type="string", format="binary")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Аватар не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="string", example="not_found"),
 *             @OA\Property(property="message", type="string", example="Аватар не найден")
 *         )
 *     )
 * )
 */
class GetPublicAvatarPath {}
