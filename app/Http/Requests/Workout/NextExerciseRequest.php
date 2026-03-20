<?php

namespace App\Http\Requests\Workout;

use App\Http\Requests\ApiFormRequest;

class NextExerciseRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_exercise_id' => 'required|exists:exercises,id',
            'weight_used' => 'nullable|numeric|min:1|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'current_exercise_id.required' => 'ID текущего упражнения обязателен',
            'current_exercise_id.exists' => 'Упражнение не найдено',
            'weight_used.numeric' => 'Вес должен быть числом',
            'weight_used.min' => 'Вес должен быть не менее 1 кг',
            'weight_used.max' => 'Вес не может превышать 500 кг',
        ];
    }
}
