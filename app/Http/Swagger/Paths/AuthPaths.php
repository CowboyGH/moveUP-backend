<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Регистрация нового пользователя",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Успешная регистрация",
 *         @OA\JsonContent(ref="#/components/schemas/RegisterResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class AuthPaths {}

/**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Авторизация пользователя",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешная авторизация",
 *         @OA\JsonContent(ref="#/components/schemas/LoginResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неверные учетные данные",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Email не подтвержден",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class LoginPath {}

/**
 * @OA\Post(
 *     path="/api/logout",
 *     summary="Выход из системы",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный выход",
 *         @OA\JsonContent(ref="#/components/schemas/LogoutResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 */
class LogoutPath {}

/**
 * @OA\Post(
 *     path="/api/refresh",
 *     summary="Обновление токена доступа",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Токен успешно обновлен",
 *         @OA\JsonContent(ref="#/components/schemas/RefreshResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 */
class RefreshPath {}

/**
 * @OA\Get(
 *     path="/api/me",
 *     summary="Получение информации о текущем пользователе",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(ref="#/components/schemas/MeResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован. Возможные причины: истекший токен, невалидный токен или сессия завершена из-за неактивности",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *                 @OA\Schema(ref="#/components/schemas/InactivityErrorResponse")
 *             }
 *         )
 *     )
 * )
 */
class MePath {}
