<?php

namespace App\Http\Requests\Workout;

use App\Http\Requests\ApiFormRequest;

class StartWorkoutRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workout_id' => 'required|integer'
        ];
    }

    public function messages(): array
    {
        return [
            'workout_id.required' => 'ID тренировки обязателен',
            'workout_id.integer' => 'ID тренировки должен быть числом',
        ];
    }
}
