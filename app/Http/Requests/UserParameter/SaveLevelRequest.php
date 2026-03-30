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
            'level_id' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'level_id.required' => 'ID уровня обязателен',
            'level_id.integer' => 'ID уровня должен быть целым числом',
            'level_id.min' => 'ID уровня должен быть больше 0',
        ];
    }
}
