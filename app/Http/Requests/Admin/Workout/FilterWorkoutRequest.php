<?php

namespace App\Http\Requests\Admin\Workout;

use App\Http\Requests\Admin\BaseFilterRequest;

class FilterWorkoutRequest extends BaseFilterRequest
{
    public function rules(): array
    {
        return parent::rules();
    }
}
