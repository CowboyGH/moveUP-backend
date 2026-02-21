<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Http\FormRequest;

class VerifyResetCodeRequest extends ApiFormRequest
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
            'code' => 'required|string',
        ];
    }
    public function messages(): array
    {
        return [
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'email.email' => 'Введите корректный адрес электронной почты.',
            'code.required' => 'Поле "Код" обязательно для заполнения.',
        ];
    }
}
