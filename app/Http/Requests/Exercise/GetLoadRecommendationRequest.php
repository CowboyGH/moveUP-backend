<?php

namespace App\Http\Requests\Exercise;

use App\Http\Requests\ApiFormRequest;

class GetLoadRecommendationRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exercise_id' => 'required|exists:exercises,id',
        ];
    }
}
