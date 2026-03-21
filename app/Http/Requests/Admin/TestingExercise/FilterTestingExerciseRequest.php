<?php

namespace App\Http\Requests\Admin\TestingExercise;

use App\Http\Requests\Admin\BaseFilterRequest;

class FilterTestingExerciseRequest extends BaseFilterRequest
{
    public function rules(): array
    {
        return parent::rules();
    }

    public function messages(): array
    {
        return [
            'exercise_id.exists' => 'Выбранное упражнение не существует',
        ];
    }
}
