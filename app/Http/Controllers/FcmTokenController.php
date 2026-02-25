<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FcmTokenController extends Controller
{
    /**
     * Сохранить или обновить FCM токен пользователя
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'device_type' => 'nullable|string|in:ios,android,web',
            'device_name' => 'nullable|string|max:255'
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }

        try {
            $user->updateFcmToken($request->fcm_token);

            $this->saveDeviceInfo($user, $request);

            Log::info('FCM token saved', [
                'user_id' => $user->id,
                'device_type' => $request->device_type
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM токен успешно сохранен'
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка сохранения FCM токена: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при сохранении токена'
            ], 500);
        }
    }

    /**
     * Удалить FCM токен пользователя
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }

        try {
            // Если токен совпадает с текущим - удаляем
            if ($user->fcm_token === $request->fcm_token) {
                $user->updateFcmToken(null);

                Log::info('FCM token removed', ['user_id' => $user->id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'FCM токен удален'
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка удаления FCM токена: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении токена'
            ], 500);
        }
    }

}
