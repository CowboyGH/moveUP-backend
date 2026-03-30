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
            'user_workout_id' => 'required|integer|min:1',  // Убираем exists
            'exercise_id' => 'required|integer|min:1',       // Убираем exists
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
            'user_workout_id.integer' => 'ID тренировки должен быть целым числом',
            'user_workout_id.min' => 'ID тренировки должен быть больше 0',
            'exercise_id.required' => 'ID упражнения обязателен',
            'exercise_id.integer' => 'ID упражнения должен быть целым числом',
            'exercise_id.min' => 'ID упражнения должен быть больше 0',
            'reaction.required' => 'Оценка обязательна',
            'reaction.in' => 'Оценка должна быть: good, normal или bad',
            'sets_completed.integer' => 'Количество подходов должно быть целым числом',
            'sets_completed.min' => 'Количество подходов не может быть отрицательным',
            'reps_completed.integer' => 'Количество повторений должно быть целым числом',
            'reps_completed.min' => 'Количество повторений не может быть отрицательным',
            'weight_used.numeric' => 'Вес должен быть числом',
            'weight_used.min' => 'Вес не может быть отрицательным',
            'notes.max' => 'Заметки не могут быть длиннее 500 символов',
        ];
    }
}
