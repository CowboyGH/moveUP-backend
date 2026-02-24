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
            'goal_id' => 'required|exists:goals,id',
        ];
    }

    public function messages(): array
    {
        return [
            'goal_id.required' => 'ID цели обязательно',
            'goal_id.exists' => 'Выбранная цель не существует',
        ];
    }
}
