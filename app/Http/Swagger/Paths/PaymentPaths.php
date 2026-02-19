<?php

namespace App\Http\Swagger\Paths;

/**
 * @OA\Post(
 *     path="/api/payment/subscription",
 *     summary="Оплата подписки",
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
 *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Подписка или карта не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 */
class PaymentPaths {}
