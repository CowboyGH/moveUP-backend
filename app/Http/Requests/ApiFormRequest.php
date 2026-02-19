<?php

namespace App\Http\Requests;

use App\Http\Responses\ErrorResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class ApiFormRequest extends FormRequest
{
    /**
     * Автоматическое удаление пробелов.
     */
    protected function prepareForValidation()
    {
        $this->merge(array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $this->all()));
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
