<?php

namespace App\Http\Requests\UserParameter;

use Illuminate\Foundation\Http\FormRequest;

class SaveLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'level_id' => 'required|exists:levels,id',
        ];
    }

    public function messages(): array
    {
        return [
            'level_id.required' => 'ID уровня обязателен',
            'level_id.exists' => 'Выбранный уровень не существует',
        ];
    }
}
