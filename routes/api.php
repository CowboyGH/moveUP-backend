<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Billing\PaymentController;
use App\Http\Controllers\Billing\SavedCardController;
use App\Http\Controllers\Billing\SubscriptionController;
use App\Http\Controllers\Onboarding\UserParameterController;
use App\Http\Controllers\Onboarding\UserProgressController;
use App\Http\Controllers\Profile\ProfileAvatarController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\ProfileDetailController;
use App\Http\Controllers\Profile\ProfileSecurityController;
use App\Http\Controllers\Profile\ProfileStatisticsController;
use App\Http\Controllers\Tests\TestAttemptController;
use App\Http\Controllers\Tests\TestingController;
use App\Http\Controllers\Workouts\Execution\WorkoutExecutionController;
use App\Http\Controllers\Workouts\WorkoutController;
use App\Http\Controllers\Workouts\WorkoutStartController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:auth-attempts')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-email', [EmailVerificationController::class, 'verifyEmail']);
    Route::post('/verify-reset-code', [PasswordResetController::class, 'verifyResetCode']);
});

Route::middleware('throttle:auth-codes')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/resend-verification-code', [EmailVerificationController::class, 'resendVerificationCode']);
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/resend-reset-code', [PasswordResetController::class, 'resendResetCode']);
});

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

Route::get('/subscriptions', [SubscriptionController::class, 'index']);
Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show']);

Route::get('/user-parameters/references', [UserParameterController::class, 'getAllReferences']);

Route::post('/user-parameters/goal', [UserParameterController::class, 'saveGoal']);
Route::post('/user-parameters/anthropometry', [UserParameterController::class, 'saveAnthropometry']);
Route::post('/user-parameters/level', [UserParameterController::class, 'saveLevel']);

Route::middleware(['jwt.custom', 'track.activity'])->prefix('profile')->group(function () {
    Route::get('/user', [ProfileDetailController::class, 'user']);
    Route::get('/active-subscription', [ProfileDetailController::class, 'activeSubscription']);
    Route::get('/my-cards', [ProfileDetailController::class, 'myCards']);
    Route::get('/user-parameters', [ProfileDetailController::class, 'userParameters']);
    Route::get('/history', [ProfileDetailController::class, 'history']);
    Route::get('/phase', [ProfileDetailController::class, 'phase']);

    Route::get('/', [ProfileController::class, 'show']);
    Route::put('/', [ProfileController::class, 'update']);
    Route::post('/avatar', [ProfileAvatarController::class, 'update']);
    Route::delete('/avatar', [ProfileAvatarController::class, 'destroy']);
    Route::post('/change-password', [ProfileSecurityController::class, 'changePassword']);
    Route::delete('/', [ProfileController::class, 'destroy']);

    Route::get('statistics', [ProfileStatisticsController::class, 'index']);
    Route::get('statistics/volume', [ProfileStatisticsController::class, 'volume']);
    Route::get('statistics/frequency', [ProfileStatisticsController::class, 'frequency']);
    Route::get('statistics/trend', [ProfileStatisticsController::class, 'trend']);
    Route::get('statistics/exercises', [ProfileStatisticsController::class, 'exercises']);
    Route::get('statistics/workouts', [ProfileStatisticsController::class, 'workouts']);
});

Route::post('/refresh', [AuthController::class, 'refresh']);

Route::middleware(['jwt.custom', 'track.activity'])->group(function () {
    Route::get('/testings', [TestingController::class, 'index']);
    Route::get('/workouts', [WorkoutController::class, 'index']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/cancel-subscription', [SubscriptionController::class, 'cancel']);

    Route::post('/tests/{testing}/start', [TestAttemptController::class, 'start']);
    Route::post('/test-attempts/{attempt}/result', [TestAttemptController::class, 'storeResult']);
    Route::post('/test-attempts/{attempt}/complete', [TestAttemptController::class, 'complete']);

    Route::get('/user-parameters/me', [UserParameterController::class, 'getMyParameters']);
    Route::post('/user/weekly-goal', [UserProgressController::class, 'updateWeeklyGoal']);

    Route::post('/workouts/start', [WorkoutStartController::class, 'start']);
    Route::post('workouts/{userWorkout}/abandon', [WorkoutStartController::class, 'abandon']);

    Route::prefix('workout-execution')->group(function () {
        Route::get('/{userWorkout}', [WorkoutExecutionController::class, 'show'])->name('workout-execution.show');
        Route::post('/{userWorkout}/next-warmup', [WorkoutExecutionController::class, 'nextWarmup'])->name('workout-execution.next-warmup');
        Route::post('/{userWorkout}/save-exercise-result', [WorkoutExecutionController::class, 'saveExerciseResult'])->name('workout-execution.save-exercise-result');
        Route::post('/{userWorkout}/complete', [WorkoutExecutionController::class, 'complete'])->name('workout-execution.complete');

        Route::post('/{userWorkout}/start-warmup', [WorkoutExecutionController::class, 'startWarmup']);
        Route::post('/{userWorkout}/complete-warmup', [WorkoutExecutionController::class, 'completeWarmup']);
    });
});

Route::middleware(['jwt.custom', 'track.activity'])->prefix('payment')->group(function () {
    Route::post('subscription', [PaymentController::class, 'processPayment']);
    Route::get('cards', [SavedCardController::class, 'getSavedCards']);
    Route::post('cards/save', [SavedCardController::class, 'simpleSaveCard']);
    Route::delete('cards/{cardId}', [SavedCardController::class, 'deleteCard']);
    Route::post('cards/{cardId}/default', [SavedCardController::class, 'setDefaultCard']);
});
