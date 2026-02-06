<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ], [
            'name.required' => 'Поле "Имя" обязательно для заполнения.',
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'email.email' => 'Введите корректный адрес электронной почты.',
            'email.unique' => 'Этот email уже зарегистрирован.',
            'password.required' => 'Поле "Пароль" обязательно для заполнения.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => Role::where('name', 'user')->first()->id,
        ]);

        $verificationCode = $user->generateEmailVerificationCode();

        Mail::to($user->email)->send(new VerificationCodeMail($verificationCode));

        return response()->json([
            'success' => true,
            'message' => 'Регистрация прошла успешно. Проверьте вашу почту для получения кода подтверждения.',
            'user' => $user
        ], 201);
    }

    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ], [
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'code.required' => 'Поле "Код" обязательно для заполнения.',
            'code.size' => 'Код должен содержать 6 символов.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не найден.'
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email уже подтвержден.'
            ], 400);
        }

        if (!$user->verifyEmailCode($request->code)) {
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

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'password.required' => 'Поле "Пароль" обязательно для заполнения.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Неверные учетные данные.'
            ], 401);
        }

        $user = Auth::user();

        if (!$user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email не подтвержден.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $user
        ]);
    }

    public function logout()
    {
        Auth::logout();

        return response()->json([
            'success' => true,
            'message' => 'Успешный выход из системы.'
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'success' => true,
            'access_token' => Auth::refresh(),
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Поле "Email" обязательно для заполнения.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не найден.'
            ], 404);
        }

        $resetCode = $user->generatePasswordResetCode();

        Mail::to($user->email)->send(new PasswordResetMail($resetCode));

        return response()->json([
            'success' => true,
            'message' => 'Код для сброса пароля отправлен на вашу почту.'
        ]);
    }

    public function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ], [
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'code.required' => 'Поле "Код" обязательно для заполнения.',
            'code.size' => 'Код должен содержать 6 символов.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не найден.'
            ], 404);
        }

        if (!$user->verifyPasswordResetCode($request->code)) {
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

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'code.required' => 'Поле "Код" обязательно для заполнения.',
            'code.size' => 'Код должен содержать 6 символов.',
            'password.required' => 'Поле "Пароль" обязательно для заполнения.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password.confirmed' => 'Пароли не совпадают.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не найден.'
            ], 404);
        }

        if (!$user->verifyPasswordResetCode($request->code)) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный или истекший код сброса.'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $user->clearPasswordResetCode();

        return response()->json([
            'success' => true,
            'message' => 'Пароль успешно сброшен.'
        ]);
    }

    public function me()
    {
        return response()->json([
            'success' => true,
            'user' => Auth::user()
        ]);
    }
}
