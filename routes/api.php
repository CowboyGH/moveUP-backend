<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\GuestTestController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\SavedCardController;
use App\Http\Controllers\Profile\ProfileAvatarController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\ProfileSecurityController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TestAttemptController;
use App\Http\Controllers\UserParameterController;
use App\Http\Controllers\UserProgressController;
use App\Http\Controllers\WorkoutExecution\WorkoutExecutionController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-email', [EmailVerificationController::class, 'verifyEmail']);
Route::post('/resend-verification-code', [EmailVerificationController::class, 'resendVerificationCode']);
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/verify-reset-code', [PasswordResetController::class, 'verifyResetCode']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
Route::post('/resend-reset-code', [PasswordResetController::class, 'resendResetCode']);

Route::get('/subscriptions', [SubscriptionController::class, 'index']);
Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show']);

Route::get('/testings', [App\Http\Controllers\TestingController::class, 'index']);
Route::get('/workouts', [App\Http\Controllers\WorkoutController::class, 'index']);

Route::get('/user-parameters/references', [UserParameterController::class, 'getAllReferences']);

Route::post('/user-parameters/goal', [UserParameterController::class, 'saveGoal']);
Route::post('/user-parameters/anthropometry', [UserParameterController::class, 'saveAnthropometry']);
Route::post('/user-parameters/level', [UserParameterController::class, 'saveLevel']);

Route::prefix('guest')->group(function () {
    Route::post('/tests/{testing}/start', [GuestTestController::class, 'start']);
    Route::post('/test-attempts/{attempt}/result', [GuestTestController::class, 'storeResult']);
    Route::post('/test-attempts/{attempt}/complete', [GuestTestController::class, 'complete']);
});

Route::middleware(['jwt.custom', 'track.activity'])->prefix('profile')->group(function () {
    Route::get('/user', [App\Http\Controllers\ProfileDetailController::class, 'user']);
    Route::get('/active-subscription', [App\Http\Controllers\ProfileDetailController::class, 'activeSubscription']);
    Route::get('/my-cards', [App\Http\Controllers\ProfileDetailController::class, 'myCards']);
    Route::get('/user-parameters', [App\Http\Controllers\ProfileDetailController::class, 'userParameters']);
    Route::get('/history', [App\Http\Controllers\ProfileDetailController::class, 'history']);
    Route::get('/phase', [App\Http\Controllers\ProfileDetailController::class, 'phase']);

    Route::get('/', [ProfileController::class, 'show']);
    Route::put('/', [ProfileController::class, 'update']);
    Route::post('/avatar', [ProfileAvatarController::class, 'update']);
    Route::delete('/avatar', [ProfileAvatarController::class, 'destroy']);
    Route::post('/change-password', [ProfileSecurityController::class, 'changePassword']);
    Route::delete('/', [ProfileController::class, 'destroy']);

    Route::get('statistics', [App\Http\Controllers\ProfileStatisticsController::class, 'index']);
    Route::get('statistics/volume', [App\Http\Controllers\ProfileStatisticsController::class, 'volume']);
    Route::get('statistics/frequency', [App\Http\Controllers\ProfileStatisticsController::class, 'frequency']);
    Route::get('statistics/trend', [App\Http\Controllers\ProfileStatisticsController::class, 'trend']);
    Route::get('statistics/exercises', [App\Http\Controllers\ProfileStatisticsController::class, 'exercises']);
    Route::get('statistics/workouts', [App\Http\Controllers\ProfileStatisticsController::class, 'workouts']);
});

Route::middleware(['jwt.custom', 'track.activity'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/cancel-subscription', [App\Http\Controllers\SubscriptionController::class, 'cancel']);

    Route::post('/tests/{testing}/start', [TestAttemptController::class, 'start']);
    Route::post('/test-attempts/{attempt}/result', [TestAttemptController::class, 'storeResult']);
    Route::post('/test-attempts/{attempt}/complete', [TestAttemptController::class, 'complete']);

    Route::get('/user-parameters/me', [UserParameterController::class, 'getMyParameters']);
    Route::post('/user/weekly-goal', [UserProgressController::class, 'updateWeeklyGoal']);

    Route::post('/workouts/start', [App\Http\Controllers\WorkoutStartController::class, 'start']);
    Route::post('workouts/{userWorkout}/abandon', [App\Http\Controllers\WorkoutStartController::class, 'abandon']);

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
