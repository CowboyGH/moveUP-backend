<?php

namespace App\Services\Onboarding;

use App\Models\Equipment;
use App\Models\Goal;
use App\Models\Level;
use App\Models\User;
use App\Models\UserParameter;
use App\Services\GuestDataService;
use App\Services\PhaseService;
use App\Services\WorkoutGeneration\WorkoutGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserParameterService
{
    public function __construct(
        private readonly GuestDataService $guestService,
        private readonly PhaseService $phaseService,
        private readonly WorkoutGeneratorService $workoutGenerator
    ) {}

    public function references(): array
    {
        return [
            'goals' => Goal::select('id', 'name')->get(),
            'levels' => Level::select('id', 'name')->get(),
            'equipment' => Equipment::select('id', 'name')->get(),
        ];
    }

    public function saveGoal(Request $request, int $goalId): array|UserParameter
    {
        if ($request->user()) {
            $user = $request->user();
            $parameters = UserParameter::firstOrNew(['user_id' => $user->id]);
            $parameters->goal_id = $goalId;
            $parameters->save();

            $this->regenerateWorkouts($user, true);

            return $parameters;
        }

        return $this->saveGuestField($request, 'goal_id', $goalId);
    }

    public function saveAnthropometry(Request $request, array $data): array|UserParameter
    {
        if ($request->user()) {
            $user = $request->user();
            $parameters = UserParameter::firstOrNew(['user_id' => $user->id]);
            $equipmentChanged = $parameters->exists && $parameters->equipment_id != ($data['equipment_id'] ?? null);

            $parameters->fill($data);
            $parameters->save();

            $force = $equipmentChanged || !$this->allParametersFilled($user);
            $this->regenerateWorkouts($user, $force);

            return $parameters;
        }

        return $this->saveGuestFields($request, $data);
    }

    public function saveLevel(Request $request, int $levelId): array|UserParameter
    {
        if ($request->user()) {
            $user = $request->user();
            $parameters = UserParameter::firstOrNew(['user_id' => $user->id]);
            $parameters->level_id = $levelId;
            $parameters->save();

            $this->regenerateWorkouts($user, true);

            return $parameters;
        }

        return $this->saveGuestField($request, 'level_id', $levelId);
    }

    public function getMyParameters(User $user): ?UserParameter
    {
        return $user->userParameters()
            ->with(['goal', 'level', 'equipment'])
            ->first();
    }

    private function saveGuestField(Request $request, string $field, int $value): array
    {
        $guestId = $this->guestService->getGuestId($request);

        return [
            'guest_id' => $guestId,
            'guest_data' => $this->guestService->updateGuestField($guestId, $field, $value),
        ];
    }

    private function saveGuestFields(Request $request, array $data): array
    {
        $guestId = $this->guestService->getGuestId($request);

        return [
            'guest_id' => $guestId,
            'guest_data' => $this->guestService->updateGuestFields($guestId, $data),
        ];
    }

    private function allParametersFilled(User $user): bool
    {
        $params = $user->userParameters;

        return $params && $params->goal_id && $params->level_id && $params->equipment_id;
    }

    private function regenerateWorkouts(User $user, bool $force = false): void
    {
        Log::info('User parameter workout regeneration requested', [
            'user_id' => $user->id,
            'force' => $force,
            'all_parameters_filled' => $this->allParametersFilled($user),
        ]);

        if (!$this->allParametersFilled($user)) {
            return;
        }

        $currentProgress = $user->currentProgress();
        if (!$currentProgress) {
            $currentProgress = $this->phaseService->assignInitialPhase($user);
        }

        if ($force) {
            $deleted = $user->userWorkouts()
                ->where('status', 'started')
                ->delete();

            Log::info('Deleted started workouts before user parameter regeneration', [
                'user_id' => $user->id,
                'deleted_count' => $deleted,
            ]);
        }

        $hasActiveWorkouts = $user->userWorkouts()
            ->where('status', 'started')
            ->exists();

        if ($force || !$hasActiveWorkouts) {
            $workouts = $this->workoutGenerator->generateForPhase($user, $currentProgress->phase);

            if ($workouts->isNotEmpty()) {
                $this->workoutGenerator->assignWorkoutsToUser($user, $workouts);
                Log::info('Generated workouts after user parameter update', [
                    'user_id' => $user->id,
                    'workouts_count' => $workouts->count(),
                ]);
            }
        }
    }
}
