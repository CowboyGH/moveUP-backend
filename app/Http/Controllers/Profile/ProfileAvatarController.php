<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateAvatarRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProfileAvatarController extends Controller
{
    public function update(UpdateAvatarRequest $request): JsonResponse
    {
        $user = auth()->user();

        try {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');

            $user->update(['avatar' => $path]);

            return ApiResponse::success('Аватар успешно загружен', [
                'avatar_url' => asset('storage/' . $path),
                'avatar_path' => $path,
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при загрузке аватара',
                500
            );
        }
    }

    public function destroy(): JsonResponse
    {
        $user = auth()->user();

        if (!$user->avatar) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Аватар не найден',
                404
            );
        }

        try {
            Storage::disk('public')->delete($user->avatar);

            $user->update(['avatar' => null]);

            return ApiResponse::success('Аватар удален', [
                'avatar_url' => null,
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при удалении аватара',
                500
            );
        }
    }
}
