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
            'workout_id' => 'required|integer|min:1',
            'reactions' => 'required|array|min:1',
            'reactions.*.exercise_id' => 'required|integer|min:1',
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
            'workout_id.integer' => 'ID тренировки должен быть целым числом',
            'workout_id.min' => 'ID тренировки должен быть больше 0',
            'reactions.required' => 'Необходимо указать оценки для упражнений',
            'reactions.array' => 'Оценки должны быть массивом',
            'reactions.min' => 'Должна быть хотя бы одна оценка',
            'reactions.*.exercise_id.required' => 'ID упражнения обязателен',
            'reactions.*.exercise_id.integer' => 'ID упражнения должен быть целым числом',
            'reactions.*.exercise_id.min' => 'ID упражнения должен быть больше 0',
            'reactions.*.reaction.required' => 'Оценка упражнения обязательна',
            'reactions.*.reaction.in' => 'Оценка должна быть: good, normal или bad',
            'reactions.*.performance.sets_completed.integer' => 'Количество подходов должно быть целым числом',
            'reactions.*.performance.sets_completed.min' => 'Количество подходов не может быть отрицательным',
            'reactions.*.performance.sets_completed.max' => 'Количество подходов не может превышать 10',
            'reactions.*.performance.reps_completed.integer' => 'Количество повторений должно быть целым числом',
            'reactions.*.performance.reps_completed.min' => 'Количество повторений не может быть отрицательным',
            'reactions.*.performance.reps_completed.max' => 'Количество повторений не может превышать 50',
            'reactions.*.performance.weight_used.numeric' => 'Вес должен быть числом',
            'reactions.*.performance.weight_used.min' => 'Вес не может быть отрицательным',
            'reactions.*.performance.weight_used.max' => 'Вес не может превышать 500 кг',
        ];
    }
}
