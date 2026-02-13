<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ResendVerificationRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'email.email' => 'Введите корректный адрес электронной почты.',
        ];
    }

    /**
     * Проверка существования email и что email еще не подтвержден
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$validator->errors()->any()) {
                $user = User::where('email', $this->email)->first();

                if (!$user) {
                    $validator->errors()->add('email', 'Пользователь с таким email не найден.');
                    return;
                }

                if ($user->email_verified_at) {
                    $validator->errors()->add('email', 'Email уже подтвержден.');
                }
            }
        });
    }
}
