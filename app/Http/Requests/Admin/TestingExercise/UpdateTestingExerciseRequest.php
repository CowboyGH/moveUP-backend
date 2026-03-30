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
            'title' => 'sometimes|string|min:1|max:255',
            'description' => 'sometimes|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'Название не может быть длиннее 255 символов',
            'title.min' => 'Название не может быть короче 1 символа',
        ];
    }
}
