<?php

namespace App\Http\Requests\Admin\Exercise;

use App\Http\Requests\Admin\BaseFilterRequest;

class FilterExerciseRequest extends BaseFilterRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'muscle_group' => 'nullable|string|max:100',
            'equipment_id' => 'nullable|integer|exists:equipments,id',
            'has_workouts' => 'nullable|boolean',
        ]);
    }

    public function messages(): array
    {
        return [
            'equipment_id.exists' => 'Выбранное оборудование не существует',
        ];
    }
}
