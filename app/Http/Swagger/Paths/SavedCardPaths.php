<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Get(
 *     path="/api/payment/cards",
 *     summary="Список сохраненных карт",
 *     description="Получение списка всех сохраненных карт пользователя",
 *     tags={"Cards"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(ref="#/components/schemas/CardsListResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 */
class SavedCardPaths {}

/**
 * @OA\Post(
 *     path="/api/payment/cards/save",
 *     summary="Сохранение карты",
 *     description="Сохранение новой банковской карты пользователя. Карта сохраняется всегда, так как это специальный эндпоинт для сохранения.",
 *     tags={"Cards"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/SaveCardRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Карта успешно сохранена",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Карта успешно сохранена"),
 *             @OA\Property(property="card_saved", type="boolean", example=true),
 *             @OA\Property(
 *                 property="card",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=12),
 *                 @OA\Property(property="last_four", type="string", example="7777")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Ошибка при сохранении",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 */
class SaveCardPath {}

/**
 * @OA\Delete(
 *     path="/api/payment/cards/{cardId}",
 *     summary="Удаление карты",
 *     description="Удаление сохраненной карты пользователя",
 *     tags={"Cards"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="cardId",
 *         in="path",
 *         required=true,
 *         description="ID карты",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Карта успешно удалена",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Карта успешно удалена")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Карта не найдена",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="string", example=false),
 *             @OA\Property(property="message", type="string", example="Карта не найдена")
 *         )
 *     )
 * )
 */
class DeleteCardPath {}

/**
 * @OA\Post(
 *     path="/api/payment/cards/{cardId}/default",
 *     summary="Установка основной карты",
 *     description="Установка карты по умолчанию для пользователя",
 *     tags={"Cards"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="cardId",
 *         in="path",
 *         required=true,
 *         description="ID карты",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Основная карта изменена",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Основная карта изменена")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Карта не найдена",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="string", example=false),
 *             @OA\Property(property="message", type="string", example="Карта не найдена")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Ошибка при изменении",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Ошибка при изменении основной карты")
 *         )
 *     )
 * )
 */
class SetDefaultCardPath {}
