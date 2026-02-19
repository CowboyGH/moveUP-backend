<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ErrorResponse
{
    /**
     * Создать JSON ответ с ошибкой
     *
     * @param string $code Код ошибки для фронтенда
     * @param string $message Сообщение для пользователя
     * @param int $status HTTP статус
     * @param array|null $errors Детальные ошибки (для 422)
     * @return JsonResponse
     */
    public static function make(
        string $code,
        string $message,
        int $status = 400,
        ?array $errors = null
    ): JsonResponse {
        $response = [
            'code' => $code,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Коды ошибок для авторизации
     */
    const INVALID_CREDENTIALS = 'invalid_credentials';
    const TOKEN_EXPIRED = 'token_expired';
    const SESSION_EXPIRED_INACTIVITY = 'session_expired_inactivity';
    const SESSION_EXPIRED_ABSOLUTE = 'session_expired_absolute';
    const UNAUTHORIZED = 'unauthorized';
    const EMAIL_NOT_VERIFIED = 'email_not_verified';
    const FORBIDDEN = 'forbidden';

    /**
     * Коды ошибок для ресурсов
     */
    const NOT_FOUND = 'not_found';
    const VALIDATION_FAILED = 'validation_failed';
    const RATE_LIMITED = 'rate_limited';
    const CONFLICT = 'conflict';
    const SERVER_ERROR = 'server_error';

    /**
     * Коды ошибок для платежей
     */
    const PAYMENT_FAILED = 'payment_failed';
    const CARD_DECLINED = 'card_declined';
    const INSUFFICIENT_FUNDS = 'insufficient_funds';
}
