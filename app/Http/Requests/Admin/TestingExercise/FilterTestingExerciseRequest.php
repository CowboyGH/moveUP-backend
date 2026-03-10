<?php

namespace App\Http\Requests\Admin\TestingExercise;

use App\Http\Requests\Admin\BaseFilterRequest;

class FilterTestingExerciseRequest extends BaseFilterRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'exercise_id' => 'nullable|integer|exists:exercises,id',
            'has_testings' => 'nullable|boolean',
        ]);
    }

    public function messages(): array
    {
        return [
            'exercise_id.exists' => 'Выбранное упражнение не существует',
        ];
    }
}
