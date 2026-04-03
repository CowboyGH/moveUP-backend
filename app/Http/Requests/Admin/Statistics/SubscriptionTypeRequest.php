<?php

namespace App\Http\Requests\Admin\Statistics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Responses\ErrorResponse;

class SubscriptionTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'subscription_type' => 'required|string|max:255',
            'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 10),
        ];
    }

    public function messages(): array
    {
        return [
            'subscription_type.required' => 'Тип подписки обязателен',
            'subscription_type.string' => 'Тип подписки должен быть строкой',
            'subscription_type.max' => 'Тип подписки не может быть длиннее 255 символов',
            'year.integer' => 'Год должен быть целым числом',
            'year.min' => 'Год должен быть не ранее 2000',
            'year.max' => 'Год не может быть позже ' . (date('Y') + 10),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ErrorResponse::make(
                'validation_failed',
                'Ошибка валидации',
                422,
                $validator->errors()->toArray()
            )
        );
    }
}
