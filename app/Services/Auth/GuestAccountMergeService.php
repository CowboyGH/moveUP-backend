<?php

namespace App\Services\Auth;

use App\Models\TestAttempt;
use App\Models\TestResult;
use App\Models\User;
use App\Models\UserParameter;
use App\Services\GuestDataService;
use App\Services\PhaseService;
use App\Services\WorkoutGeneration\WorkoutGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GuestAccountMergeService
{
    public function __construct(
        private readonly GuestDataService $guestService,
        private readonly PhaseService $phaseService,
        private readonly WorkoutGeneratorService $workoutGenerator
    ) {}

    public function mergeFromRequest(User $user, Request $request): void
    {
        $this->transferGuestDataToUser($user, $this->guestService->getGuestId($request));
    }

    private function transferGuestDataToUser(User $user, ?string $guestId): void
    {
        if (!$guestId) {
            return;
        }

        $hasParams = false;
        $hasTests = false;

        if ($this->guestService->hasGuestData($guestId)) {
            $hasParams = $this->transferGuestParameters($user, $guestId);
        }

        if ($this->guestService->hasGuestTestResults($guestId)) {
            $hasTests = $this->transferGuestTestResults($user, $guestId);
        }

        $user->refresh();
        $params = $user->userParameters;

        Log::info('Guest data merge parameter check completed', [
            'user_id' => $user->id,
            'has_params_object' => (bool) $params,
            'goal_id' => $params->goal_id ?? null,
            'level_id' => $params->level_id ?? null,
            'equipment_id' => $params->equipment_id ?? null,
            'has_params' => $hasParams,
        ]);

        if ($params && $params->goal_id && $params->level_id && $params->equipment_id) {
            $this->ensureUserHasPhaseAndWorkouts($user);
        } else {
            Log::info('Guest data merge skipped phase assignment because parameters are incomplete', [
                'user_id' => $user->id,
                'goal_id' => $params->goal_id ?? null,
                'level_id' => $params->level_id ?? null,
                'equipment_id' => $params->equipment_id ?? null,
            ]);
        }

        $this->guestService->clearGuestData($guestId);

        Log::info('Guest data merged into user account', [
            'guest_id' => $guestId,
            'user_id' => $user->id,
            'has_params' => $hasParams,
            'has_tests' => $hasTests,
        ]);
    }

    private function transferGuestParameters(User $user, string $guestId): bool
    {
        $guestData = $this->guestService->getGuestData($guestId);

        if (empty($guestData)) {
            return false;
        }

        Log::info('Merging guest parameters into user account', [
            'guest_id' => $guestId,
            'user_id' => $user->id,
        ]);

        $parameters = UserParameter::firstOrNew(['user_id' => $user->id]);
        $fillableFields = ['goal_id', 'level_id', 'equipment_id', 'height', 'weight', 'age', 'gender'];
        $updated = false;

        foreach ($fillableFields as $field) {
            if (isset($guestData[$field])) {
                $oldValue = $parameters->$field;
                $parameters->$field = $guestData[$field];

                if ($oldValue != $guestData[$field]) {
                    $updated = true;
                    Log::debug('Guest parameter field updated', [
                        'user_id' => $user->id,
                        'field' => $field,
                    ]);
                }
            }
        }

        if (!$updated) {
            Log::info('Guest parameters did not change user account', ['user_id' => $user->id]);

            return false;
        }

        $parameters->save();
        $parameters->refresh();

        Log::info('Guest parameters saved for user account', [
            'user_id' => $user->id,
            'goal_id' => $parameters->goal_id,
            'level_id' => $parameters->level_id,
            'equipment_id' => $parameters->equipment_id,
        ]);

        return true;
    }

    private function transferGuestTestResults(User $user, string $guestId): bool
    {
        $guestTests = $this->guestService->getGuestTestResults($guestId);
        $completedTests = array_filter($guestTests, fn($test) => ($test['status'] ?? '') === 'completed');

        if (empty($completedTests)) {
            return false;
        }

        Log::info('Merging guest test results into user account', [
            'guest_id' => $guestId,
            'user_id' => $user->id,
            'tests_count' => count($completedTests),
        ]);

        DB::transaction(function () use ($user, $completedTests) {
            foreach ($completedTests as $testData) {
                $attempt = TestAttempt::create([
                    'testing_id' => $testData['testing_id'],
                    'started_at' => $testData['started_at'] ?? now(),
                    'completed_at' => $testData['completed_at'] ?? now(),
                    'pulse' => $testData['pulse'] ?? null,
                ]);

                if (!empty($testData['results'])) {
                    foreach ($testData['results'] as $result) {
                        TestResult::create([
                            'user_id' => $user->id,
                            'testing_id' => $testData['testing_id'],
                            'test_attempt_id' => $attempt->id,
                            'testing_exercise_id' => $result['testing_exercise_id'],
                            'result_value' => $result['result_value'],
                            'test_date' => now()->toDateString(),
                        ]);
                    }
                }

                Log::info('Guest test result merged into user account', [
                    'user_id' => $user->id,
                    'attempt_id' => $attempt->id,
                ]);
            }
        });

        return true;
    }

    private function ensureUserHasPhaseAndWorkouts(User $user): void
    {
        Log::info('Ensuring user has phase and generated workouts after guest merge', ['user_id' => $user->id]);

        $params = $user->userParameters;
        if (!$params) {
            Log::error('Cannot assign phase after guest merge because user parameters are missing', [
                'user_id' => $user->id,
            ]);

            return;
        }

        if (!$params->goal_id || !$params->level_id || !$params->equipment_id) {
            Log::info('Cannot assign phase after guest merge because parameters are incomplete', [
                'user_id' => $user->id,
                'goal_id' => $params->goal_id ?? null,
                'level_id' => $params->level_id ?? null,
                'equipment_id' => $params->equipment_id ?? null,
            ]);

            return;
        }

        $currentProgress = $user->currentProgress();
        if (!$currentProgress) {
            $currentProgress = $this->phaseService->assignInitialPhase($user);
            Log::info('Initial phase assigned after guest merge', [
                'user_id' => $user->id,
                'phase_id' => $currentProgress->phase_id,
            ]);
        }

        if ($user->userWorkouts()->count() !== 0) {
            return;
        }

        $generatedWorkouts = $this->workoutGenerator->generateForPhase($user, $currentProgress->phase);

        if ($generatedWorkouts->isNotEmpty()) {
            $this->workoutGenerator->assignWorkoutsToUser($user, $generatedWorkouts);
            Log::info('Workouts generated after guest merge', [
                'user_id' => $user->id,
                'workouts_count' => $generatedWorkouts->count(),
            ]);
        } else {
            Log::warning('Workout generation returned no workouts after guest merge', ['user_id' => $user->id]);
        }
    }
}
