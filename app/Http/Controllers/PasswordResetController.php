<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyResetCodeRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;

class PasswordResetController extends Controller
{
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $validated = $request->validated();
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Пользователь с таким email не найден.',
                404
            );
        }

        $resetCode = $user->generatePasswordResetCode();
        Mail::to($user->email)->send(new PasswordResetMail($resetCode));

        return ApiResponse::success('Код для сброса пароля отправлен на вашу почту.');
    }

    public function verifyResetCode(VerifyResetCodeRequest $request)
    {
        $validated = $request->validated();
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Пользователь с таким email не найден.',
                404
            );
        }

        if (!$user->verifyPasswordResetCode($validated['code'])) {
            return ApiResponse::error(
                ErrorResponse::VALIDATION_FAILED,
                'Неверный или истекший код.',
                400
            );
        }

        return ApiResponse::success('Код сброса пароля успешно подтвержден.');
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $validated = $request->validated();
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Пользователь с таким email не найден.',
                404
            );
        }

        if (!$user->verifyPasswordResetCode($validated['code'])) {
            return ApiResponse::error(
                ErrorResponse::VALIDATION_FAILED,
                'Неверный или истекший код.',
                400
            );
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $user->clearPasswordResetCode();

        return ApiResponse::success('Пароль успешно сброшен.');
    }
}
