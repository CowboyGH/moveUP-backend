<?php

namespace App\Http\Requests\Tests;

use App\Http\Requests\ApiFormRequest;

class StoreTestResultRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'testing_exercise_id' => 'required|exists:testing_exercises,id',
            'result_value' => 'required|integer|between:1,4',
        ];
    }
}
