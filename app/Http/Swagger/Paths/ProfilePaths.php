<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Get(
 *     path="/api/profile",
 *     summary="Получение полного профиля пользователя",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(ref="#/components/schemas/ProfileResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class ProfilePaths {}

/**
 * @OA\Put(
 *     path="/api/profile",
 *     summary="Обновление профиля (имя и email)",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/UpdateProfileRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Профиль успешно обновлен",
 *         @OA\JsonContent(ref="#/components/schemas/UpdateProfileResponse")
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
class UpdateProfilePath {}

/**
 * @OA\Post(
 *     path="/api/profile/change-password",
 *     summary="Смена пароля",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ChangePasswordRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Пароль успешно изменен",
 *         @OA\JsonContent(ref="#/components/schemas/ChangePasswordResponse")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Неверный текущий пароль",
 *         @OA\JsonContent(ref="#/components/schemas/InvalidCurrentPasswordResponse")
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
class ChangePasswordPath {}

/**
 * @OA\Delete(
 *     path="/api/profile",
 *     summary="Удаление профиля",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Профиль успешно удален",
 *         @OA\JsonContent(ref="#/components/schemas/DeleteProfileResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class DeleteProfilePath {}
