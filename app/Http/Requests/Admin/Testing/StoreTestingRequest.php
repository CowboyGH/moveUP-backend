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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
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
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif',
            'image.max' => 'Максимальный размер изображения 5MB',
            'category_ids.*.exists' => 'Указанная категория не существует',
            'exercise_ids.*.exists' => 'Указанное упражнение не существует',
        ];
    }
}
