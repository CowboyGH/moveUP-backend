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
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1Qi..."),
 *             @OA\Property(property="token_type", type="string", example="bearer"),
 *             @OA\Property(property="expires_in", type="integer", example=3600),
 *             @OA\Property(property="refresh_expires_in", type="integer", example=2592000, description="30 дней в секундах"),
 *             @OA\Property(
 *                 property="session",
 *                 type="object",
 *                 @OA\Property(property="lifetime_days", type="integer", example=30),
 *                 @OA\Property(property="inactivity_limit_days", type="integer", example=7),
 *                 @OA\Property(property="access_token_expires_in_minutes", type="integer", example=60)
 *             ),
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Иван Иванов"),
 *                 @OA\Property(property="email", type="string", example="user@example.com")
 *             )
 *         )
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
 *          response=401,
 *          description="Не авторизован. Возможные причины: истекший токен, невалидный токен или сессия завершена из-за неактивности",
 *          @OA\JsonContent(
 *              oneOf={
 *                  @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *                  @OA\Schema(ref="#/components/schemas/InactivityErrorResponse")
 *              }
 *          )
 *      ),
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
 *          response=401,
 *          description="Не авторизован. Возможные причины: истекший токен, невалидный токен или сессия завершена из-за неактивности",
 *          @OA\JsonContent(
 *              oneOf={
 *                  @OA\Schema(ref="#/components/schemas/ErrorResponse"),
 *                  @OA\Schema(ref="#/components/schemas/InactivityErrorResponse")
 *              }
 *          )
 *      ),
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
