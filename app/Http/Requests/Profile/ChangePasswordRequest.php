<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiFormRequest;

class ChangePasswordRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'old_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'max:12',
                'confirmed',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]+$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'old_password.required' => 'Введите текущий пароль.',
            'new_password.required' => 'Введите новый пароль.',
            'new_password.min' => 'Пароль должен содержать минимум 8 символов.',
            'new_password.max' => 'Пароль должен содержать максимум 12 символов.',
            'new_password.confirmed' => 'Пароли не совпадают.',
            'new_password.regex' => 'Пароль должен содержать только латинские буквы и цифры.',
        ];
    }
}
