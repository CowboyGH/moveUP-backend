<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Equipment;
use App\Models\Goal;
use App\Models\Level;
use App\Models\User;
use App\Models\UserParameter;
use App\Services\GuestDataService;
use App\Services\PhaseService;
use App\Services\WorkoutGeneration\WorkoutGeneratorService;
use App\Http\Requests\UserParameter\SaveGoalRequest;
use App\Http\Requests\UserParameter\SaveAnthropometryRequest;
use App\Http\Requests\UserParameter\SaveLevelRequest;
use App\Http\Requests\UserParameter\UpdateUserParameterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserParameterController extends Controller
{
    private GuestDataService $guestService;
    private PhaseService $phaseService;
    private WorkoutGeneratorService $workoutGenerator;

    public function __construct(
        GuestDataService $guestService,
        PhaseService $phaseService,
        WorkoutGeneratorService $workoutGenerator
    ) {
        $this->guestService = $guestService;
        $this->phaseService = $phaseService;
        $this->workoutGenerator = $workoutGenerator;
    }
    public function getGoals(): JsonResponse
    {
        $goals = Goal::all();

        return ApiResponse::success('Список целей получен', $goals);
    }
    public function getLevels(): JsonResponse
    {
        $levels = Level::all();

        return ApiResponse::success('Список уровней получен', $levels);
    }
    public function getEquipment(): JsonResponse
    {
        $equipment = Equipment::all();

        return ApiResponse::success('Список оборудования получен', $equipment);
    }
    public function getAllReferences(): JsonResponse
    {
        $data = [
            'goals' => Goal::select('id', 'name')->get(),
            'levels' => Level::select('id', 'name',)->get(),
            'equipment' => Equipment::select('id', 'name')->get(),
        ];

        return ApiResponse::success('Справочные данные получены', $data);
    }

    public function saveGoal(SaveGoalRequest $request)
    {
        if ($request->user()) {
            $user = $request->user();
            $parameters = UserParameter::firstOrNew(['user_id' => $user->id]);
            $parameters->goal_id = $request->goal_id;
            $parameters->save();

            $this->regenerateWorkouts($user, true);

            return ApiResponse::success('Цель сохранена', $parameters);
        }

        $guestId = $this->guestService->getGuestId($request);
        $guestData = $this->guestService->updateGuestField($guestId, 'goal_id', $request->goal_id);

        return ApiResponse::success('Цель сохранена для гостя', [
            'guest_id' => $guestId,
            'guest_data' => $guestData
        ])->withCookie(cookie('guest_id', $guestId, 60 * 24 * 30));
    }

    public function saveAnthropometry(SaveAnthropometryRequest $request)
    {
        $data = $request->getData();

        if ($request->user()) {
            $user = $request->user();
            $parameters = UserParameter::firstOrNew(['user_id' => $user->id]);

            $equipmentChanged = $parameters->exists && $parameters->equipment_id != ($data['equipment_id'] ?? null);

            $parameters->fill($data);
            $parameters->save();

            $force = $equipmentChanged || !$this->allParametersFilled($user);
            $this->regenerateWorkouts($user, $force);

            return ApiResponse::success('Антропометрия сохранена', $parameters);
        }

        $guestId = $this->guestService->getGuestId($request);
        $guestData = $this->guestService->updateGuestFields($guestId, $data);

        return ApiResponse::success('Антропометрия сохранена для гостя', [
            'guest_id' => $guestId,
            'guest_data' => $guestData
        ])->withCookie(cookie('guest_id', $guestId, 60 * 24 * 30));
    }

    public function saveLevel(SaveLevelRequest $request)
    {
        if ($request->user()) {
            $user = $request->user();
            $parameters = UserParameter::firstOrNew(['user_id' => $user->id]);
            $parameters->level_id = $request->level_id;
            $parameters->save();

            $this->regenerateWorkouts($user, true);

            return ApiResponse::success('Уровень сохранен', $parameters);
        }

        $guestId = $this->guestService->getGuestId($request);
        $guestData = $this->guestService->updateGuestField($guestId, 'level_id', $request->level_id);

        return ApiResponse::success('Уровень сохранен для гостя', [
            'guest_id' => $guestId,
            'guest_data' => $guestData
        ])->withCookie(cookie('guest_id', $guestId, 60 * 24 * 30));
    }

    /**
     * Проверяет, заполнены ли все необходимые параметры
     */
    private function allParametersFilled(User $user): bool
    {
        $params = $user->userParameters;
        return $params && $params->goal_id && $params->level_id && $params->equipment_id;
    }

    /**
     * Перегенерирует тренировки пользователя при необходимости
     */
    public function regenerateWorkouts(User $user, bool $force = false): void
    {
        Log::info("🔄 regenerateWorkouts вызван", [
            'user_id' => $user->id,
            'force' => $force,
            'all_parameters_filled' => $this->allParametersFilled($user)
        ]);

        $params = $user->userParameters;

        if (!$this->allParametersFilled($user)) {
            return;
        }

        // Получаем или создаем прогресс
        $currentProgress = $user->currentProgress();
        if (!$currentProgress) {
            $currentProgress = $this->phaseService->assignInitialPhase($user);
        }

        if ($force) {
            // Принудительно удаляем старые тренировки
            $deleted = $user->userWorkouts()
                ->where('status', 'started')
                ->delete();

            Log::info("Удалено {$deleted} старых тренировок пользователя {$user->id} перед перегенерацией");
        }

        // Проверяем, есть ли активные тренировки
        $hasActiveWorkouts = $user->userWorkouts()
            ->where('status', 'started')
            ->exists();

        // Генерируем новые, если их нет или мы их удалили
        if ($force || !$hasActiveWorkouts) {
            Log::info("✅ Начинаем генерацию тренировок", [
                'user_id' => $user->id,
                'phase_id' => $currentProgress->phase_id
            ]);
            $workouts = $this->workoutGenerator->generateForPhase($user, $currentProgress->phase);

            if ($workouts->isNotEmpty()) {
                $this->workoutGenerator->assignWorkoutsToUser($user, $workouts);
                Log::info("Сгенерировано {$workouts->count()} тренировок для пользователя {$user->id}");
            }
        }
    }

    public function clearGuestData(Request $request)
    {
        $guestId = $this->guestService->getGuestId($request);

        if ($guestId && $this->guestService->hasGuestData($guestId)) {
            $this->guestService->clearGuestData($guestId);
        }

        return ApiResponse::success('Данные гостя очищены', null)
            ->withCookie(cookie()->forget('guest_id'));
    }

    public function getMyParameters(Request $request)
    {
        $parameters = $request->user()->userParameters()
            ->with(['goal', 'level', 'equipment'])
            ->first();

        if (!$parameters) {
            return ApiResponse::success('Параметры не найдены', null);
        }

        return ApiResponse::success('Параметры получены', $parameters);
    }

    public function update(UpdateUserParameterRequest $request)
    {
        $user = $request->user();
        $parameters = UserParameter::firstOrNew(['user_id' => $user->id]);

        // Проверяем, изменились ли ключевые параметры
        $goalChanged = $parameters->exists && $parameters->goal_id != $request->goal_id;
        $levelChanged = $parameters->exists && $parameters->level_id != $request->level_id;
        $equipmentChanged = $parameters->exists && $parameters->equipment_id != $request->equipment_id;

        $parameters->fill($request->getFillableData());
        $parameters->save();

        // Перегенерируем при изменении ключевых параметров
        $force = $goalChanged || $levelChanged || $equipmentChanged;
        $this->regenerateWorkouts($user, $force);

        return ApiResponse::success('Параметры обновлены', $parameters);
    }
}
