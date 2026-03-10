<?php

namespace App\Http\Requests\Admin\Workout;

use App\Http\Requests\Admin\BaseFilterRequest;

class FilterWorkoutRequest extends BaseFilterRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'phase_id' => 'nullable|integer|exists:phases,id',
            'duration_min' => 'nullable|integer|min:1',
            'duration_max' => 'nullable|integer|min:1|gte:duration_min',
            'exercises_count_min' => 'nullable|integer|min:0',
            'exercises_count_max' => 'nullable|integer|min:0',
        ]);
    }
}
