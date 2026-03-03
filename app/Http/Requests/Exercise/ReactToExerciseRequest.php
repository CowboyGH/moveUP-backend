<?php

namespace App\Http\Requests\Exercise;

use App\Http\Requests\ApiFormRequest;
use App\Models\ExerciseReaction;

class ReactToExerciseRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_workout_id' => 'required|exists:user_workouts,id',
            'exercise_id' => 'required|exists:exercises,id',
            'reaction' => 'required|in:' . implode(',', ExerciseReaction::getReactions()),
            'sets_completed' => 'nullable|integer|min:0',
            'reps_completed' => 'nullable|integer|min:0',
            'weight_used' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'user_workout_id.required' => 'ID тренировки обязателен',
            'user_workout_id.exists' => 'Тренировка не найдена',
            'exercise_id.required' => 'ID упражнения обязателен',
            'exercise_id.exists' => 'Упражнение не найдено',
            'reaction.required' => 'Оценка обязательна',
            'reaction.in' => 'Оценка должна быть: good, normal или bad',
        ];
    }
}
