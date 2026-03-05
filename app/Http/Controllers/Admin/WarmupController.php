<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Warmup\StoreWarmupRequest;
use App\Http\Requests\Admin\Warmup\UpdateWarmupRequest;
use App\Http\Requests\Admin\Warmup\UploadWarmupImageRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Warmup;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class WarmupController extends Controller
{
    /**
     * Получить список всех разминок
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
                'image_url' => $warmup->image_url,
                'workouts_count' => $warmup->workouts_count,
                'created_at' => $warmup->created_at?->toISOString(),
                'updated_at' => $warmup->updated_at?->toISOString(),
            ];
        });

        return ApiResponse::data($formattedWarmups);
    }

    /**
     * Создать новую разминку
     */
    public function store(StoreWarmupRequest $request): JsonResponse
    {
        try {
            $data = $request->except('image');

            // Сохраняем изображение
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('warmups', 'public');
                $data['image'] = $path;
            }

            $warmup = Warmup::create($data);

            $responseData = [
                'id' => $warmup->id,
                'name' => $warmup->name,
                'description' => $warmup->description,
                'image' => $warmup->image,
                'image_url' => $warmup->image_url,
                'workouts_count' => 0,
                'created_at' => $warmup->created_at?->toISOString(),
                'updated_at' => $warmup->updated_at?->toISOString(),
            ];

            return ApiResponse::success('Разминка успешно создана', $responseData, 201);

        } catch (\Exception $e) {
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при создании разминки: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Получить разминку по ID
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
                'image_url' => $workout->image_url,
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
            'image_url' => $warmup->image_url,
            'workouts_count' => $warmup->workouts_count,
            'workouts' => $formattedWorkouts,
            'created_at' => $warmup->created_at?->toISOString(),
            'updated_at' => $warmup->updated_at?->toISOString(),
        ];

        return ApiResponse::data($data);
    }

    /**
     * Обновить разминку
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

        try {
            $data = $request->except('image');

            // Обрабатываем новое изображение
            if ($request->hasFile('image')) {
                // Удаляем старое изображение
                if ($warmup->image) {
                    Storage::disk('public')->delete($warmup->image);
                }

                $path = $request->file('image')->store('warmups', 'public');
                $data['image'] = $path;
            }

            $warmup->update($data);

            return ApiResponse::success('Разминка успешно обновлена', [
                'id' => $warmup->id,
                'name' => $warmup->name,
                'description' => $warmup->description,
                'image' => $warmup->image,
                'image_url' => $warmup->image_url,
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при обновлении разминки: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Удалить разминку
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

        try {
            // Удаляем изображение
            if ($warmup->image) {
                Storage::disk('public')->delete($warmup->image);
            }

            $warmup->delete();

            return ApiResponse::success('Разминка успешно удалена');

        } catch (\Exception $e) {
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при удалении разминки: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Загрузить изображение для разминки
     */
    public function uploadImage(UploadWarmupImageRequest $request, int $id): JsonResponse
    {
        $warmup = Warmup::find($id);

        if (!$warmup) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Разминка не найдена',
                404
            );
        }

        try {
            // Удаляем старое изображение
            if ($warmup->image) {
                Storage::disk('public')->delete($warmup->image);
            }

            // Сохраняем новое изображение
            $path = $request->file('image')->store('warmups', 'public');
            $warmup->update(['image' => $path]);

            return ApiResponse::success('Изображение успешно загружено', [
                'image' => $warmup->image,
                'image_url' => $warmup->image_url,
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при загрузке изображения: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Получить изображение разминки (публичный доступ)
     */
    public function getImage(int $id)
    {
        $warmup = Warmup::find($id);

        // Если разминка не найдена или изображение отсутствует
        if (!$warmup || !$warmup->image) {
            return $this->getDefaultImage();
        }

        // Проверяем, что путь к файлу не пустой
        $path = Storage::disk('public')->path($warmup->image);

        // Проверяем существование файла
        if (empty($warmup->image) || !file_exists($path)) {
            return $this->getDefaultImage();
        }

        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
            'Cache-Control' => 'public, max-age=86400'
        ]);
    }

    /**
     * Получить дефолтное изображение
     */
    private function getDefaultImage()
    {
        $defaultPath = public_path('images/default-warmup.png');

        if (file_exists($defaultPath)) {
            return response()->file($defaultPath, [
                'Content-Type' => mime_content_type($defaultPath),
                'Cache-Control' => 'public, max-age=86400'
            ]);
        }

        return response()->json([
            'code' => ErrorResponse::NOT_FOUND,
            'message' => 'Изображение не найдено'
        ], 404);
    }
}
