<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'password' => [
                'required',
                'string',
                'min:8',
                'max:64',
                'confirmed',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]+$/',
            ],
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = User::where('email', $this->email)->first();

            // Проверяем, существует ли пользователь и совпадает ли пароль со старым
            if ($user && $user->password && Hash::check($this->password, $user->password)) {
                $validator->errors()->add('password', 'Новый пароль не должен совпадать со старым паролем.');
            }
        });
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();

        // Форматируем ошибки в единый формат
        $formattedErrors = [];
        foreach ($errors as $field => $messages) {
            $formattedErrors[$field] = $messages;
        }

        throw new HttpResponseException(
            response()->json([
                'code' => 'validation_failed',
                'message' => 'Ошибка валидации',
                'errors' => $formattedErrors
            ], 422)
        );
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Поле email обязательно',
            'email.email' => 'Введите корректный адрес электронной почты.',

            'code.required' => 'Поле "Код" обязательно для заполнения.',
            'code.size' => 'Неверный код.',
            'code.regex' => 'Неверный код.',

            'password.required' => 'Поле пароль обязательно',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
            'password.max' => 'Пароль должен содержать максимум 64 символа.',
            'password.confirmed' => 'Пароли не совпадают.',
            'password.regex' => 'Пароль должен содержать только латинские буквы и цифры.',
        ];
    }
}
