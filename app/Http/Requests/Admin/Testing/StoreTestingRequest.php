<?php

namespace App\Http\Requests\Admin\Testing;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|string|max:50',
            'image' => 'required|string|max:255',
            'is_active' => 'boolean',
            'category_ids' => 'array',
            'category_ids.*' => 'exists:categories,id',
            'exercise_ids' => 'array',
            'exercise_ids.*' => 'exists:testing_exercises,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название теста обязательно',
            'title.max' => 'Название не может быть длиннее 255 символов',
            'description.required' => 'Описание теста обязательно',
            'duration_minutes.required' => 'Длительность теста обязательна',
            'image.required' => 'Изображение теста обязательно',
            'category_ids.*.exists' => 'Указанная категория не существует',
            'exercise_ids.*.exists' => 'Указанное упражнение не существует',
        ];
    }
}
