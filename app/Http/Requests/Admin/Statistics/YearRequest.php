<?php

namespace App\Http\Requests\Admin\Statistics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Responses\ErrorResponse;

class YearRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 10),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'year.integer' => 'Год должен быть целым числом',
            'year.min' => 'Год должен быть не ранее 2000',
            'year.max' => 'Год не может быть позже ' . (date('Y') + 10),
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
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
