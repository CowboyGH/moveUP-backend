<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\Auth\VerifyEmailRequest;

class EmailVerificationController extends Controller
{
    /**
     * Подтверждение email через код
     */
    public function verifyEmail(VerifyEmailRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email уже подтвержден.'
            ], 400);
        }

        if (!$user->verifyEmailCode($validated['code'])) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный или истекший код подтверждения.',
            ], 400);
        }

        $user->email_verified_at = now();
        $user->save();

        $user->clearEmailVerificationCode();

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Email успешно подтвержден.',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $user->fresh()
        ]);
    }
}
