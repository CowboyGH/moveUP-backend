<?php

namespace App\Http\Requests\Workout;

use App\Http\Requests\ApiFormRequest;

class NextWarmupRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_warmup_id' => 'nullable|exists:warmups,id',
        ];
    }

    public function messages(): array
    {
        return [
            'current_warmup_id.exists' => 'Упражнение разминки не найдено',
        ];
    }
}
