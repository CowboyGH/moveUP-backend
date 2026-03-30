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
            'title' => 'required|string|min:1|max:255',
            'description' => 'required|string',
            'muscle_group' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название тестового упражнения обязательно',
            'title.max' => 'Название не может быть длиннее 255 символов',
            'title.min' => 'Название не может быть короче 1 символа',
            'description.required' => 'Описание упражнения обязательно',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif, webp',
            'image.max' => 'Максимальный размер изображения 5MB',
        ];
    }
}
