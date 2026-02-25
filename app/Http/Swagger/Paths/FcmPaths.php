<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Post(
 *     path="/api/fcm/token",
 *     summary="Сохранить FCM токен",
 *     description="Сохраняет или обновляет FCM токен текущего пользователя",
 *     tags={"FCM"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/FcmTokenRequest")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Токен успешно сохранен",
 *         @OA\JsonContent(ref="#/components/schemas/FcmTokenResponse")
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class FcmPaths {}

/**
 * @OA\Delete(
 *     path="/api/fcm/token",
 *     summary="Удалить FCM токен",
 *     description="Удаляет FCM токен текущего пользователя (при выходе из приложения)",
 *     tags={"FCM"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/FcmTokenDeleteRequest")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Токен удален",
 *         @OA\JsonContent(ref="#/components/schemas/FcmTokenDeleteResponse")
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class FcmTokenDeletePath {}
