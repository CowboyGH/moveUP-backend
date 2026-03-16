<?php

namespace App\Http\Controllers;

use App\Services\PhaseService;
use App\Models\User;
use App\Models\Phase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PhaseController extends Controller
{
    protected $phaseService;

    public function __construct(PhaseService $phaseService)
    {
        $this->phaseService = $phaseService;
    }

    public function getCurrentPhase(Request $request): JsonResponse
    {
        $user = $request->user();
        $progress = $this->phaseService->getUserPhaseProgress($user);

        return response()->json([
            'success' => true,
            'message' => 'Текущая фаза пользователя',
            'data' => $progress
        ]);
    }

    public function getAllPhases(): JsonResponse
    {
        $phases = Phase::orderBy('order_number')->get();

        return response()->json([
            'success' => true,
            'message' => 'Список всех фаз',
            'data' => $phases
        ]);
    }

    public function getPhaseDetails(Phase $phase): JsonResponse
    {
        $phase->load(['workouts' => function ($query) {
            $query->where('is_active', 1);
        }]);

        $formattedWorkouts = $phase->workouts->map(function ($workout) {
            return [
                'id' => $workout->id,
                'phase_id' => $workout->phase_id,
                'title' => $workout->title,
                'description' => $workout->description,
                'duration_minutes' => $workout->duration_minutes,
                'type' => $workout->type,
                'is_active' => $workout->is_active,
                'image' => $workout->image,
                'image_url' => $workout->image_url,
                'created_at' => $workout->created_at,
                'updated_at' => $workout->updated_at,
            ];
        });

        $data = [
            'id' => $phase->id,
            'name' => $phase->name,
            'description' => $phase->description,
            'duration_days' => $phase->duration_days,
            'order_number' => $phase->order_number,
            'created_at' => $phase->created_at,
            'updated_at' => $phase->updated_at,
            'workouts' => $formattedWorkouts,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Детальная информация о фазе',
            'data' => $data
        ]);
    }
}
