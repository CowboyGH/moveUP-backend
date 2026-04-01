<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Get(
 *     path="/api/profile/user",
 *     summary="Получить информацию о пользователе",
 *     description="Возвращает основную информацию о текущем пользователе",
 *     tags={"Profile Details"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(ref="#/components/schemas/ProfileUserResponse")
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
class ProfileDetailPaths {}

/**
 * @OA\Get(
 *     path="/api/profile/active-subscription",
 *     summary="Получить активную подписку пользователя",
 *     description="Возвращает информацию об активной подписке или сообщение об ее отсутствии",
 *     tags={"Profile Details"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(ref="#/components/schemas/ProfileActiveSubscriptionResponse"),
 *                 @OA\Schema(ref="#/components/schemas/ProfileEmptyResponse")
 *             }
 *         )
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
class ProfileActiveSubscriptionPath {}

/**
 * @OA\Get(
 *     path="/api/profile/my-cards",
 *     summary="Получить сохраненные карты пользователя",
 *     description="Возвращает список сохраненных карт или сообщение об их отсутствии",
 *     tags={"Profile Details"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(ref="#/components/schemas/ProfileCardsResponse"),
 *                 @OA\Schema(ref="#/components/schemas/ProfileEmptyResponse")
 *             }
 *         )
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
class ProfileMyCardsPath {}

/**
 * @OA\Get(
 *     path="/api/profile/user-parameters",
 *     summary="Получить параметры пользователя",
 *     description="Возвращает параметры пользователя (цель, уровень, оборудование, антропометрию) или сообщение о статусе заполнения",
 *     tags={"Profile Details"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(ref="#/components/schemas/ProfileUserParametersResponse"),
 *                 @OA\Schema(ref="#/components/schemas/ProfileEmptyResponse"),
 *                 @OA\Schema(ref="#/components/schemas/ProfileIncompleteParametersResponse")
 *             }
 *         )
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
class ProfileUserParametersPath {}

/**
 * @OA\Get(
 *     path="/api/profile/history",
 *     summary="Получить историю пользователя",
 *     description="Возвращает историю подписок, тренировок и тестов или сообщение об отсутствии истории",
 *     tags={"Profile Details"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(ref="#/components/schemas/ProfileHistoryResponse"),
 *                 @OA\Schema(ref="#/components/schemas/ProfileEmptyHistoryResponse")
 *             }
 *         )
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
class ProfileHistoryPath {}

/**
 * @OA\Get(
 *     path="/api/profile/phase",
 *     summary="Получить текущую фазу и прогресс",
 *     description="Возвращает информацию о текущей фазе пользователя, прогрессе и рекомендациях",
 *     tags={"Profile Details"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(ref="#/components/schemas/ProfilePhaseResponse"),
 *                 @OA\Schema(ref="#/components/schemas/ProfileEmptyResponse")
 *             }
 *         )
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
class ProfilePhasePath {}
