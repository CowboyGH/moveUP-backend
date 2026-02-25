<?php

namespace App\Http\Requests\Admin\Workout;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phase_id' => 'nullable|exists:phases,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1|max:300',
            'is_active' => 'boolean',
            'exercises' => 'sometimes|array',
            'exercises.*.exercise_id' => 'required_with:exercises|exists:exercises,id',
            'exercises.*.sets' => 'required_with:exercises|integer|min:1|max:100',
            'exercises.*.reps' => 'required_with:exercises|string|max:50',
            'exercises.*.order_number' => 'required_with:exercises|integer|min:1',
            'warmups' => 'sometimes|array',
            'warmups.*.warmup_id' => 'required_with:warmups|exists:warmups,id',
            'warmups.*.order_number' => 'required_with:warmups|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название тренировки обязательно',
            'title.max' => 'Название не может превышать 255 символов',
            'description.required' => 'Описание тренировки обязательно',
            'duration_minutes.required' => 'Длительность тренировки обязательна',
            'duration_minutes.min' => 'Длительность должна быть не менее 1 минуты',
            'duration_minutes.max' => 'Длительность не может превышать 300 минут',
            'phase_id.exists' => 'Выбранная фаза не существует',
            'exercises.*.exercise_id.exists' => 'Упражнение не найдено',
            'exercises.*.sets.min' => 'Количество подходов должно быть не менее 1',
            'warmups.*.warmup_id.exists' => 'Разминка не найдена',
        ];
    }
}
