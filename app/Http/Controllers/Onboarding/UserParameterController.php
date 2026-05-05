<?php

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Http\Requests\Onboarding\SaveAnthropometryRequest;
use App\Http\Requests\Onboarding\SaveGoalRequest;
use App\Http\Requests\Onboarding\SaveLevelRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Equipment;
use App\Models\Goal;
use App\Models\Level;
use App\Services\Onboarding\UserParameterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserParameterController extends Controller
{
    public function __construct(
        private readonly UserParameterService $userParameterService
    ) {}

    public function getAllReferences(): JsonResponse
    {
        return ApiResponse::success('Справочные данные получены', $this->userParameterService->references());
    }

    public function saveGoal(SaveGoalRequest $request): JsonResponse
    {
        if (!Goal::find($request->goal_id)) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Цель не найдена',
                404
            );
        }

        $result = $this->userParameterService->saveGoal($request, $request->goal_id);

        if ($request->user()) {
            return ApiResponse::success('Цель сохранена', $result);
        }

        return ApiResponse::success('Цель сохранена для гостя', $result)
            ->withCookie(cookie('guest_id', $result['guest_id'], 60 * 24 * 30));
    }

    public function saveAnthropometry(SaveAnthropometryRequest $request): JsonResponse
    {
        $data = $request->getData();

        if (!Equipment::find($data['equipment_id'])) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Оборудование не найдено',
                404
            );
        }

        $result = $this->userParameterService->saveAnthropometry($request, $data);

        if ($request->user()) {
            return ApiResponse::success('Антропометрия сохранена', $result);
        }

        return ApiResponse::success('Антропометрия сохранена для гостя', $result)
            ->withCookie(cookie('guest_id', $result['guest_id'], 60 * 24 * 30));
    }

    public function saveLevel(SaveLevelRequest $request): JsonResponse
    {
        if (!Level::find($request->level_id)) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Уровень не найден',
                404
            );
        }

        $result = $this->userParameterService->saveLevel($request, $request->level_id);

        if ($request->user()) {
            return ApiResponse::success('Уровень сохранен', $result);
        }

        return ApiResponse::success('Уровень сохранен для гостя', $result)
            ->withCookie(cookie('guest_id', $result['guest_id'], 60 * 24 * 30));
    }

    public function getMyParameters(Request $request): JsonResponse
    {
        $parameters = $this->userParameterService->getMyParameters($request->user());

        if (!$parameters) {
            return ApiResponse::success('Параметры не найдены', null);
        }

        return ApiResponse::success('Параметры получены', $parameters);
    }
}
