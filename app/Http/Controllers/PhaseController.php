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
            'data' => $progress
        ]);
    }

    public function getAllPhases(): JsonResponse
    {
        $phases = Phase::orderBy('order_number')->get();

        return response()->json([
            'success' => true,
            'data' => $phases
        ]);
    }

    public function getPhaseDetails(Phase $phase): JsonResponse
    {
        $phase->load('workouts');

        return response()->json([
            'success' => true,
            'data' => $phase
        ]);
    }
}
