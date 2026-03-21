<?php

namespace App\Http\Requests\Admin\Exercise;

use App\Http\Requests\Admin\BaseFilterRequest;

class FilterExerciseRequest extends BaseFilterRequest
{
    public function rules(): array
    {
        return parent::rules();
    }

    public function messages(): array
    {
        return [
            'equipment_id.exists' => 'Выбранное оборудование не существует',
        ];
    }
}
