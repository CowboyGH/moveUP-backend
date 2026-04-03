<?php

namespace App\Http\Requests\Admin\Statistics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Responses\ErrorResponse;

class PeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'period' => 'nullable|integer|in:1,3,6,12',
        ];
    }

    public function messages(): array
    {
        return [
            'period.integer' => 'Период должен быть целым числом',
            'period.in' => 'Период должен быть 1, 3, 6 или 12 месяцев',
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
