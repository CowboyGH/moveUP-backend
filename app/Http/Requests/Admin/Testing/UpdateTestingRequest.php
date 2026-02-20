<?php

namespace App\Http\Requests\Admin\Testing;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'duration_minutes' => 'sometimes|string|max:50',
            'image' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
            'category_ids' => 'sometimes|array',
            'category_ids.*' => 'exists:categories,id',
            'exercise_ids' => 'sometimes|array',
            'exercise_ids.*' => 'exists:testing_exercises,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'Название не может быть длиннее 255 символов',
            'title.string' => 'Название может содержать только буквы',
            'category_ids.*.exists' => 'Указанная категория не существует',
            'exercises.*.id.exists' => 'Указанное упражнение не существует',
        ];
    }
}
