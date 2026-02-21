<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'code' => 'required|string|size:6|regex:/^[0-9]+$/',
            'password' => ['required', 'string', 'min:8', 'max:12', 'confirmed', 'regex:/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]+$/'],
        ];
    }
    public function messages(): array
    {
        return [
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'email.email' => 'Введите корректный адрес электронной почты.',

            'code.required' => 'Поле "Код" обязательно для заполнения.',
            'code.size' => 'Неверный код.',
            'code.regex' => 'Неверный код.',

            'password.required' => 'Поле "Пароль" обязательно для заполнения.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password.max' => 'Пароль должен содержать максимум 12 символов.',
            'password.confirmed' => 'Пароли не совпадают.',
            'password.regex' => 'Пароль должен содержать только латинские буквы и цифры.',
        ];
    }

}
