<?php

namespace App\Http\Requests\Workout;

use App\Http\Requests\ApiFormRequest;

class CompleteWorkoutWithReactionsRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workout_id' => 'required|exists:workouts,id',
            'reactions' => 'required|array|min:1',
            'reactions.*.exercise_id' => 'required|exists:exercises,id',
            'reactions.*.reaction' => 'required|in:good,normal,bad',
            'reactions.*.performance.sets_completed' => 'nullable|integer|min:0|max:10',
            'reactions.*.performance.reps_completed' => 'nullable|integer|min:0|max:50',
            'reactions.*.performance.weight_used' => 'nullable|numeric|min:0|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'workout_id.required' => 'ID тренировки обязателен',
            'workout_id.exists' => 'Тренировка не найдена',
            'reactions.required' => 'Необходимо указать оценки для упражнений',
            'reactions.*.exercise_id.required' => 'ID упражнения обязателен',
            'reactions.*.exercise_id.exists' => 'Упражнение не найдено',
            'reactions.*.reaction.required' => 'Оценка упражнения обязательна',
            'reactions.*.reaction.in' => 'Оценка должна быть: good, normal или bad',
        ];
    }
}
