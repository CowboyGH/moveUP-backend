<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\UserParameter;
use App\Services\GuestDataService;
use App\Http\Requests\UserParameter\SaveGoalRequest;
use App\Http\Requests\UserParameter\SaveAnthropometryRequest;
use App\Http\Requests\UserParameter\SaveLevelRequest;
use App\Http\Requests\UserParameter\UpdateUserParameterRequest;
use Illuminate\Http\Request;

class UserParameterController extends Controller
{
    private GuestDataService $guestService;

    public function __construct(GuestDataService $guestService)
    {
        $this->guestService = $guestService;
    }

    public function saveGoal(SaveGoalRequest $request)
    {
        if ($request->user()) {
            $user = $request->user();
            $parameters = UserParameter::firstOrNew(['user_id' => $user->id]);
            $parameters->goal_id = $request->goal_id;
            $parameters->save();

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
            $parameters->fill($data);
            $parameters->save();

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

            return ApiResponse::success('Уровень сохранен', $parameters);
        }

        $guestId = $this->guestService->getGuestId($request);
        $guestData = $this->guestService->updateGuestField($guestId, 'level_id', $request->level_id);

        return ApiResponse::success('Уровень сохранен для гостя', [
            'guest_id' => $guestId,
            'guest_data' => $guestData
        ])->withCookie(cookie('guest_id', $guestId, 60 * 24 * 30));
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
        $parameters->fill($request->getFillableData());
        $parameters->save();

        return ApiResponse::success('Параметры обновлены', $parameters);
    }
}
