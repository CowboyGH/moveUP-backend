<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'name' => ['sometimes', 'string', 'max:20', 'min:2', 'regex:/^[a-zA-Zа-яА-ЯёЁ\s]+$/u'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Имя не должно превышать 20 символов.',
            'name.min' => 'Имя не должно быть короче 2-х символов',
            'name.regex' => 'Имя может содержать только буквы и пробелы.',
            'email.email' => 'Введите корректный адрес электронной почты.',
            'email.unique' => 'Этот email уже зарегистрирован.',
        ];
    }
}
