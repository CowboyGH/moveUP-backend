<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends ApiFormRequest
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
            'name' => ['required', 'string', 'max:20', 'regex:/^[a-zA-Zа-яА-ЯёЁ\s]+$/u'],
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'max:12', 'regex:/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле "Имя" обязательно для заполнения.',
            'name.max' => 'Имя не должно превышать 20 символов.',
            'name.regex' => 'Имя может содержать только буквы и пробелы.',

            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'email.email' => 'Введите корректный адрес электронной почты.',
            'email.unique' => 'Этот email уже зарегистрирован.',

            'password.required' => 'Поле "Пароль" обязательно для заполнения.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password.max' => 'Пароль должен содержать максимум 12 символов.',
            'password.regex' => 'Неверно введен пароль или адрес почты.',
        ];
    }
}
