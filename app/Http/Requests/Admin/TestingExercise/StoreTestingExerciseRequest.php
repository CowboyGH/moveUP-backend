<?php

namespace App\Http\Requests\Admin\TestingExercise;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestingExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'description' => 'required|string',
            'image' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'Описание упражнения обязательно',
            'image.required' => 'Изображение упражнения обязательно',
        ];
    }
}
