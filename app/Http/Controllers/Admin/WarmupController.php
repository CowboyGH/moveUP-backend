<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Warmup\StoreWarmupRequest;
use App\Http\Requests\Admin\Warmup\UpdateWarmupRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Warmup;
use Illuminate\Http\JsonResponse;

class WarmupController extends Controller
{
    /**
     * Получить список всех разминок
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $warmups = Warmup::withCount('workouts')->get();

        $formattedWarmups = $warmups->map(function ($warmup) {
            return [
                'id' => $warmup->id,
                'name' => $warmup->name,
                'description' => $warmup->description,
                'image' => $warmup->image,
                'workouts_count' => $warmup->workouts_count,
                'created_at' => $warmup->created_at?->toISOString(),
                'updated_at' => $warmup->updated_at?->toISOString(),
            ];
        });

        return ApiResponse::data($formattedWarmups);
    }

    /**
     * Создать новую разминку
     *
     * @param StoreWarmupRequest $request
     * @return JsonResponse
     */
    public function store(StoreWarmupRequest $request): JsonResponse
    {
        $warmup = Warmup::create($request->validated());

        $data = [
            'id' => $warmup->id,
            'name' => $warmup->name,
            'description' => $warmup->description,
            'image' => $warmup->image,
            'workouts_count' => 0,
            'created_at' => $warmup->created_at?->toISOString(),
            'updated_at' => $warmup->updated_at?->toISOString(),
        ];

        return ApiResponse::success('Разминка успешно создана', $data, 201);
    }

    /**
     * Получить разминку по ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $warmup = Warmup::with(['workouts'])->withCount('workouts')->find($id);

        if (!$warmup) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Разминка не найдена',
                404
            );
        }

        $formattedWorkouts = $warmup->workouts->map(function ($workout) {
            return [
                'id' => $workout->id,
                'title' => $workout->title,
                'description' => $workout->description,
                'duration_minutes' => $workout->duration_minutes,
                'is_active' => $workout->is_active,
                'pivot' => [
                    'order_number' => $workout->pivot->order_number,
                ],
            ];
        });

        $data = [
            'id' => $warmup->id,
            'name' => $warmup->name,
            'description' => $warmup->description,
            'image' => $warmup->image,
            'workouts_count' => $warmup->workouts_count,
            'workouts' => $formattedWorkouts,
            'created_at' => $warmup->created_at?->toISOString(),
            'updated_at' => $warmup->updated_at?->toISOString(),
        ];

        return ApiResponse::data($data);
    }

    /**
     * Обновить разминку
     *
     * @param UpdateWarmupRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateWarmupRequest $request, int $id): JsonResponse
    {
        $warmup = Warmup::find($id);

        if (!$warmup) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Разминка не найдена',
                404
            );
        }

        $warmup->update($request->validated());

        return ApiResponse::success('Разминка успешно обновлена', $warmup);
    }

    /**
     * Удалить разминку
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $warmup = Warmup::find($id);

        if (!$warmup) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Разминка не найдена',
                404
            );
        }

        if ($warmup->workouts()->exists()) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Нельзя удалить разминку, которая используется в тренировках',
                422
            );
        }

        $warmup->delete();

        return ApiResponse::success('Разминка успешно удалена');
    }
}
