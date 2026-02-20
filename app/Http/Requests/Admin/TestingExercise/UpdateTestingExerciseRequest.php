<?php

namespace App\Http\Requests\Admin\TestingExercise;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestingExerciseRequest extends FormRequest
{

    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'description' => 'sometimes|string',
            'image' => 'sometimes|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'description.string' => 'Описание должно быть строкой',
            'image.max' => 'Изображение не может быть длиннее 255 символов',
        ];
    }
}
