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
            'exercise_id' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'exercise_id.required' => 'ID упражнения обязателен',
            'exercise_id.integer' => 'ID упражнения должен быть целым числом',
            'exercise_id.min' => 'ID упражнения должен быть больше 0',
        ];
    }
}
