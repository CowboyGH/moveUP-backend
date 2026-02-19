<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Успешный ответ
     */
    public static function success(string $message = 'Операция выполнена успешно', $data = null, int $status = 200): JsonResponse
    {
        $response = ['success' => true, 'message' => $message];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Ответ с данными (для списков)
     */
    public static function data($data, string $message = 'success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Ответ с ошибкой (использует ErrorResponse)
     */
    public static function error(string $code, string $message, int $status = 400, ?array $errors = null): JsonResponse
    {
        return ErrorResponse::make($code, $message, $status, $errors);
    }
}
