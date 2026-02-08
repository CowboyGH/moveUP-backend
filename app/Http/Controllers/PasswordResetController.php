<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\VerifyResetCodeRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;

class PasswordResetController extends Controller
{
    /**
     * Запрос на восстановление пароля
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        $resetCode = $user->generatePasswordResetCode();

        Mail::to($user->email)->send(new PasswordResetMail($resetCode));

        return response()->json([
            'success' => true,
            'message' => 'Код для сброса пароля отправлен на вашу почту.'
        ]);
    }

    /**
     * Подтверждение кода сброса пароля
     */
    public function verifyResetCode(VerifyResetCodeRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user->verifyPasswordResetCode($validated['code'])) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный или истекший код сброса.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Код сброса пароля успешно подтвержден.'
        ]);
    }

    /**
     * Сброс пароля
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user->verifyPasswordResetCode($validated['code'])) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный или истекший код сброса.'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $user->clearPasswordResetCode();

        return response()->json([
            'success' => true,
            'message' => 'Пароль успешно сброшен.'
        ]);
    }
}
