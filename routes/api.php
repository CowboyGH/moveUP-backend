<?php

use App\Http\Controllers\Admin\SubscriptionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PasswordResetController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-email', [EmailVerificationController::class, 'verifyEmail']);
Route::post('/resend-verification-code', [EmailVerificationController::class, 'resendVerificationCode']);
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/verify-reset-code', [PasswordResetController::class, 'verifyResetCode']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

Route::get('/subscriptions', [SubscriptionController::class, 'index']);
Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show']);


Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/my-subscriptions', [App\Http\Controllers\SubscriptionController::class, 'mySubscriptions']);
});

Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show']);
    Route::put('/subscriptions/{id}', [SubscriptionController::class, 'update']);
    Route::delete('/subscriptions/{id}', [SubscriptionController::class, 'destroy']);

});
