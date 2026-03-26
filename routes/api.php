<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\TestingController;
use App\Http\Controllers\Admin\TestingExerciseController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\GuestTestController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\SavedCardController;
use App\Http\Controllers\PhaseController;
use App\Http\Controllers\TestAttemptController;
use App\Http\Controllers\UserParameterController;
use App\Http\Controllers\UserProgressController;
use App\Http\Controllers\WorkoutExecution\WorkoutExecutionController;
use App\Http\Controllers\WorkoutGeneratorController;
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
Route::post('/resend-reset-code', [PasswordResetController::class, 'resendResetCode']);

Route::get('/subscriptions', [SubscriptionController::class, 'index']);
Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show']);

Route::get('/testings', [App\Http\Controllers\TestingController::class, 'index']);
Route::get('/testings/{id}', [App\Http\Controllers\TestingController::class, 'show']);
Route::get('/workouts', [App\Http\Controllers\WorkoutController::class, 'index']);

Route::get('/goals', [UserParameterController::class, 'getGoals']);
Route::get('/levels', [UserParameterController::class, 'getLevels']);
Route::get('/equipment', [UserParameterController::class, 'getEquipment']);
Route::get('/user-parameters/references', [UserParameterController::class, 'getAllReferences']);

Route::post('/user-parameters/goal', [UserParameterController::class, 'saveGoal']);
Route::post('/user-parameters/anthropometry', [UserParameterController::class, 'saveAnthropometry']);
Route::post('/user-parameters/level', [UserParameterController::class, 'saveLevel']);
Route::delete('/user-parameters/guest', [UserParameterController::class, 'clearGuestData']);

Route::get('/avatars/{userId}', [App\Http\Controllers\ProfileController::class, 'getAvatar']);

Route::prefix('guest')->group(function () {
    Route::post('/tests/{testing}/start', [GuestTestController::class, 'start']);
    Route::post('/test-attempts/{attempt}/result', [GuestTestController::class, 'storeResult']);
    Route::post('/test-attempts/{attempt}/complete', [GuestTestController::class, 'complete']);
    Route::get('/tests/history', [GuestTestController::class, 'history']);
    Route::delete('/tests/reset', [GuestTestController::class, 'reset']);
});

Route::middleware(['jwt.custom', 'track.activity'])->prefix('profile')->group(function () {
    Route::get('/', [App\Http\Controllers\ProfileController::class, 'show']);
    Route::put('/', [App\Http\Controllers\ProfileController::class, 'update']);
    Route::post('/avatar', [App\Http\Controllers\ProfileController::class, 'updateAvatar']);
    Route::delete('/avatar', [App\Http\Controllers\ProfileController::class, 'deleteAvatar']);
    Route::post('/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword']);
    Route::delete('/', [App\Http\Controllers\ProfileController::class, 'destroy']);

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
    Route::get('/my-subscriptions', [App\Http\Controllers\SubscriptionController::class, 'mySubscriptions']);
    Route::get('/my-test-history', [App\Http\Controllers\TestingController::class, 'myTestHistory']);
    Route::get('/my-workout-history', [App\Http\Controllers\WorkoutController::class, 'myWorkoutHistory']);

    Route::post('/tests/{testing}/start', [TestAttemptController::class, 'start']);
    Route::post('/test-attempts/{attempt}/result', [TestAttemptController::class, 'storeResult']);
    Route::post('/test-attempts/{attempt}/complete', [TestAttemptController::class, 'complete']);

    Route::get('/user-parameters/me', [UserParameterController::class, 'getMyParameters']);
    Route::put('/user-parameters', [UserParameterController::class, 'update']);
    Route::post('/user/weekly-goal', [UserProgressController::class, 'updateWeeklyGoal']);

    Route::post('/fcm/token', [FcmTokenController::class, 'update']);
    Route::delete('/fcm/token', [FcmTokenController::class, 'destroy']);

    Route::get('/user/current-phase', [PhaseController::class, 'getCurrentPhase']);
    Route::get('/phases', [PhaseController::class, 'getAllPhases']);
    Route::get('/phases/{phase}', [PhaseController::class, 'getPhaseDetails']);

    Route::post('/exercise/reaction', [App\Http\Controllers\ExerciseReactionController::class, 'react']);
    Route::get('/exercise/{exerciseId}/reactions/history', [App\Http\Controllers\ExerciseReactionController::class, 'history']);
    Route::get('/exercise/reactions/statistics', [App\Http\Controllers\ExerciseReactionController::class, 'statistics']);
    Route::post('/exercise/load-recommendation', [App\Http\Controllers\ExerciseReactionController::class, 'recommendation']);
    Route::post('/workouts/complete-with-adjustments', [App\Http\Controllers\WorkoutCompletionController::class, 'completeWithAdjustments']);
    Route::post('/workouts/start', [App\Http\Controllers\WorkoutStartController::class, 'start']);

    Route::prefix('workout-execution')->group(function () {
        Route::get('/{userWorkout}', [WorkoutExecutionController::class, 'show'])->name('workout-execution.show');
        Route::post('/{userWorkout}/next-warmup', [WorkoutExecutionController::class, 'nextWarmup'])->name('workout-execution.next-warmup');
        Route::post('/{userWorkout}/next-exercise', [WorkoutExecutionController::class, 'nextExercise'])->name('workout-execution.next-exercise');
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

Route::middleware(['jwt.custom', 'admin', 'track.activity'])->prefix('admin')->group(function () {

    Route::get('/overview', [App\Http\Controllers\Admin\StatisticsController::class, 'overview']);
    Route::get('/revenue', [App\Http\Controllers\Admin\StatisticsController::class, 'revenue']);
    Route::get('/subscriptions/count', [App\Http\Controllers\Admin\StatisticsController::class, 'subscriptionsCount']);
    Route::get('/subscriptions/period', [App\Http\Controllers\Admin\StatisticsController::class, 'subscriptionsByPeriod']);

    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show']);
    Route::put('/subscriptions/{id}', [SubscriptionController::class, 'update']);
    Route::post('/subscriptions/{id}/image', [SubscriptionController::class, 'updateImage'])->name('admin.subscriptions.updateImage');
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
    Route::post('/testings/{id}/image', [TestingController::class, 'updateImage']);

    Route::get('/testing-exercises', [TestingExerciseController::class, 'index']);
    Route::post('/testing-exercises', [TestingExerciseController::class, 'store']);
    Route::get('/testing-exercises/{id}', [TestingExerciseController::class, 'show']);
    Route::put('/testing-exercises/{id}', [TestingExerciseController::class, 'update']);
    Route::delete('/testing-exercises/{id}', [TestingExerciseController::class, 'destroy']);
    Route::post('/testing-exercises/{id}/image', [TestingExerciseController::class, 'updateImage']);

    Route::get('/exercises', [ExerciseController::class, 'index']);
    Route::post('/exercises', [ExerciseController::class, 'store']);
    Route::get('/exercises/{id}', [ExerciseController::class, 'show']);
    Route::put('/exercises/{id}', [ExerciseController::class, 'update']);
    Route::delete('/exercises/{id}', [ExerciseController::class, 'destroy']);
    Route::post('/exercises/{id}/image', [App\Http\Controllers\Admin\ExerciseController::class, 'uploadImage']);
    Route::get('/exercises/{id}/image', [App\Http\Controllers\Admin\ExerciseController::class, 'getImage']);

    Route::get('/warmups', [WarmupController::class, 'index']);
    Route::post('/warmups', [WarmupController::class, 'store']);
    Route::get('/warmups/{id}', [WarmupController::class, 'show']);
    Route::put('/warmups/{id}', [WarmupController::class, 'update']);
    Route::delete('/warmups/{id}', [WarmupController::class, 'destroy']);
    Route::post('/warmups/{id}/image', [App\Http\Controllers\Admin\WarmupController::class, 'uploadImage']);
    Route::get('/warmups/{id}/image', [App\Http\Controllers\Admin\WarmupController::class, 'getImage']);

    Route::get('/workouts', [WorkoutController::class, 'index']);
    Route::post('/workouts', [WorkoutController::class, 'store']);
    Route::get('/workouts/{id}', [WorkoutController::class, 'show']);
    Route::put('/workouts/{id}', [WorkoutController::class, 'update']);
    Route::delete('/workouts/{id}', [WorkoutController::class, 'destroy']);
    Route::post('/workouts/{id}/image', [App\Http\Controllers\Admin\WorkoutController::class, 'uploadImage']);
    Route::get('/workouts/{id}/image', [App\Http\Controllers\Admin\WorkoutController::class, 'getImage']);

    Route::post('/workouts/generate-for-user/{userId}', [WorkoutGeneratorController::class, 'generateForUser']);
    Route::post('/workouts/regenerate-for-user/{userId}', [WorkoutGeneratorController::class, 'regenerateForUser']);

    Route::get('/equipments', [App\Http\Controllers\Admin\EquipmentController::class, 'index']);
});
