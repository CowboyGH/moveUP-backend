<?php

namespace App\Http\Requests\UserParameter;

use Illuminate\Foundation\Http\FormRequest;

class SaveGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'goal_id' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'goal_id.required' => 'ID цели обязательно',
            'goal_id.integer' => 'ID цели должен быть целым числом',
            'goal_id.min' => 'ID цели должен быть больше 0',
        ];
    }
}
