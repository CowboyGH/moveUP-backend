<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Profile\ProfileAggregateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileAggregateService $profileAggregateService
    ) {}

    public function show(): JsonResponse
    {
        return ApiResponse::success('success', $this->profileAggregateService->build(auth()->user()));
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        $user->update($data);

        return ApiResponse::success('Профиль успешно обновлен', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
        ]);
    }

    public function destroy(): JsonResponse
    {
        $user = auth()->user();

        if ($user->avatar) {
            Storage::delete($user->avatar);
        }

        $user->delete();

        return ApiResponse::success('Профиль успешно удален');
    }
}
