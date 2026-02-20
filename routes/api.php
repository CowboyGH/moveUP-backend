<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\TestingController;
use App\Http\Controllers\Admin\TestingExerciseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\SavedCardController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-email', [EmailVerificationController::class, 'verifyEmail']);
Route::post('/resend-verification-code', [EmailVerificationController::class, 'resendVerificationCode']);
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/verify-reset-code', [PasswordResetController::class, 'verifyResetCode']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

Route::get('/subscriptions', [SubscriptionController::class, 'index']);
Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show']);


Route::middleware(['auth:api', 'track.activity'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/my-subscriptions', [App\Http\Controllers\SubscriptionController::class, 'mySubscriptions']);
});

Route::middleware(['auth:api', 'track.activity'])->prefix('payment')->group(function () {
    Route::post('subscription', [PaymentController::class, 'processPayment']);
    Route::get('cards', [SavedCardController::class, 'getSavedCards']);
    Route::post('cards/save', [SavedCardController::class, 'simpleSaveCard']);
    Route::delete('cards/{cardId}', [SavedCardController::class, 'deleteCard']);
    Route::post('cards/{cardId}/default', [SavedCardController::class, 'setDefaultCard']);
});

Route::middleware(['auth:api', 'admin', 'track.activity'])->prefix('admin')->group(function () {
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show']);
    Route::put('/subscriptions/{id}', [SubscriptionController::class, 'update']);
    Route::delete('/subscriptions/{id}', [SubscriptionController::class, 'destroy']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    Route::get('/testings', [TestingController::class, 'index']);
    Route::post('/testings', [TestingController::class, 'store']);
    Route::get('/testings/{id}', [TestingController::class, 'show']);
    Route::put('/testings/{id}', [TestingController::class, 'update']);
    Route::delete('/testings/{id}', [TestingController::class, 'destroy']);
    Route::patch('/testings/{id}/toggle-active', [TestingController::class, 'toggleActive']);

    Route::get('/testing-exercises', [TestingExerciseController::class, 'index']);
    Route::post('/testing-exercises', [TestingExerciseController::class, 'store']);
    Route::get('/testing-exercises/{id}', [TestingExerciseController::class, 'show']);
    Route::put('/testing-exercises/{id}', [TestingExerciseController::class, 'update']);
    Route::delete('/testing-exercises/{id}', [TestingExerciseController::class, 'destroy']);

});
