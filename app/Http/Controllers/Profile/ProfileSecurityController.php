<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class ProfileSecurityController extends Controller
{
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        if (!Hash::check($data['old_password'], $user->password)) {
            return ApiResponse::error(
                ErrorResponse::VALIDATION_FAILED,
                'Неверный текущий пароль',
                400
            );
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return ApiResponse::success('Пароль успешно изменен');
    }
}
