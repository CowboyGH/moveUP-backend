<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Post(
 *     path="/api/payment/subscription",
 *     summary="Оплата подписки",
 *     description="Обработка платежа за подписку. Можно использовать новую или сохраненную карту.",
 *     tags={"Payments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/SubscriptionPaymentRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешная оплата",
 *         @OA\JsonContent(ref="#/components/schemas/PaymentResponse")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Ошибка в данных карты",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Не удалось получить данные карты"),
 *             @OA\Property(property="status", type="string", example="failed")
 *         )
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
 *     @OA\Response(
 *         response=404,
 *         description="Подписка или карта не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     )
 * )
 */
class PaymentPaths {}
