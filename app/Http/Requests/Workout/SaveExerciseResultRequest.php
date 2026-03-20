<?php

namespace App\Http\Requests\Workout;

use App\Http\Requests\ApiFormRequest;

class SaveExerciseResultRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exercise_id' => 'required|exists:exercises,id',
            'reaction' => 'required|in:good,normal,bad',
            'weight_used' => 'nullable|numeric|min:1|max:500',
            'sets_completed' => 'nullable|integer|min:0|max:10',
            'reps_completed' => 'nullable|integer|min:0|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'exercise_id.required' => 'ID упражнения обязателен',
            'exercise_id.exists' => 'Упражнение не найдено',
            'reaction.required' => 'Оценка упражнения обязательна',
            'reaction.in' => 'Оценка должна быть: good, normal или bad',
            'weight_used.numeric' => 'Вес должен быть числом',
            'weight_used.min' => 'Вес должен быть не менее 1 кг',
            'weight_used.max' => 'Вес не может превышать 500 кг',
            'sets_completed.integer' => 'Количество подходов должно быть целым числом',
            'sets_completed.min' => 'Количество подходов не может быть отрицательным',
            'sets_completed.max' => 'Количество подходов не может превышать 10',
            'reps_completed.integer' => 'Количество повторений должно быть целым числом',
            'reps_completed.min' => 'Количество повторений не может быть отрицательным',
            'reps_completed.max' => 'Количество повторений не может превышать 50',
        ];
    }
}
