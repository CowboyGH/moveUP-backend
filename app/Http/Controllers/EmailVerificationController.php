<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\User;
use App\Jobs\SendVerificationEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmailVerificationController extends Controller
{
    public function verifyEmail(VerifyEmailRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if ($user->email_verified_at) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Email уже подтвержден.',
                400
            );
        }

        if (!$user->verifyEmailCode($validated['code'])) {
            return ApiResponse::error(
                ErrorResponse::VALIDATION_FAILED,
                'Неверный или истекший код подтверждения.',
                400
            );
        }

        $user->email_verified_at = now();
        $user->save();

        $user->clearEmailVerificationCode();

        $token = JWTAuth::fromUser($user);

        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $user->fresh()
        ];

        return ApiResponse::success('Email успешно подтвержден.', $data);
    }

    public function resendVerificationCode(ResendVerificationRequest $request)
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

        if ($user->email_verified_at) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Email уже подтвержден.',
                400
            );
        }
        SendVerificationEmail::dispatch($user);

        return ApiResponse::success('Новый код подтверждения отправлен на вашу почту.');
    }
}
