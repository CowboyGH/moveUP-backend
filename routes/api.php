<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\TestingController;
use App\Http\Controllers\Admin\TestingExerciseController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\SavedCardController;
use App\Http\Controllers\PhaseController;
use App\Http\Controllers\UserParameterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ExerciseController;
use App\Http\Controllers\Admin\WarmupController;
use App\Http\Controllers\Admin\WorkoutController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-email', [EmailVerificationController::class, 'verifyEmail']);
Route::post('/resend-verification-code', [EmailVerificationController::class, 'resendVerificationCode']);
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/verify-reset-code', [PasswordResetController::class, 'verifyResetCode']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

Route::get('/subscriptions', [SubscriptionController::class, 'index']);
Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show']);

Route::get('/testings', [App\Http\Controllers\TestingController::class, 'index']);
Route::get('/testings/{id}', [App\Http\Controllers\TestingController::class, 'show']);
Route::get('/workouts', [App\Http\Controllers\WorkoutController::class, 'index']);
Route::get('/workouts/{id}', [App\Http\Controllers\WorkoutController::class, 'show']);

Route::post('/user-parameters/goal', [UserParameterController::class, 'saveGoal']);
Route::post('/user-parameters/anthropometry', [UserParameterController::class, 'saveAnthropometry']);
Route::post('/user-parameters/level', [UserParameterController::class, 'saveLevel']);
Route::delete('/user-parameters/guest', [UserParameterController::class, 'clearGuestData']);


Route::middleware(['auth:api', 'track.activity'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/my-subscriptions', [App\Http\Controllers\SubscriptionController::class, 'mySubscriptions']);
    Route::get('/my-test-history', [App\Http\Controllers\TestingController::class, 'myTestHistory']);
    Route::get('/my-workout-history', [App\Http\Controllers\WorkoutController::class, 'myWorkoutHistory']);

    Route::get('/user-parameters/me', [UserParameterController::class, 'getMyParameters']);
    Route::put('/user-parameters', [UserParameterController::class, 'update']);

    Route::post('/fcm/token', [FcmTokenController::class, 'update']);
    Route::delete('/fcm/token', [FcmTokenController::class, 'destroy']);

    Route::get('/user/current-phase', [PhaseController::class, 'getCurrentPhase']);
    Route::get('/phases', [PhaseController::class, 'getAllPhases']);
    Route::get('/phases/{phase}', [PhaseController::class, 'getPhaseDetails']);
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

    Route::get('/exercises', [ExerciseController::class, 'index']);
    Route::post('/exercises', [ExerciseController::class, 'store']);
    Route::get('/exercises/{id}', [ExerciseController::class, 'show']);
    Route::put('/exercises/{id}', [ExerciseController::class, 'update']);
    Route::delete('/exercises/{id}', [ExerciseController::class, 'destroy']);

    Route::get('/warmups', [WarmupController::class, 'index']);
    Route::post('/warmups', [WarmupController::class, 'store']);
    Route::get('/warmups/{id}', [WarmupController::class, 'show']);
    Route::put('/warmups/{id}', [WarmupController::class, 'update']);
    Route::delete('/warmups/{id}', [WarmupController::class, 'destroy']);

    Route::get('/workouts', [WorkoutController::class, 'index']);
    Route::post('/workouts', [WorkoutController::class, 'store']);
    Route::get('/workouts/{id}', [WorkoutController::class, 'show']);
    Route::put('/workouts/{id}', [WorkoutController::class, 'update']);
    Route::delete('/workouts/{id}', [WorkoutController::class, 'destroy']);
});
