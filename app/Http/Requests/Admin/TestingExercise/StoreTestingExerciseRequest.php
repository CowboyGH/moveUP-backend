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
            'exercise_id' => 'required|integer|exists:exercises,id',
            'description' => 'required|string',
            'image' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'exercise_id.required' => 'Необходимо указать ID основного упражнения',
            'exercise_id.exists'   => 'Указанное упражнение не существует',
            'description.required' => 'Описание упражнения обязательно',
            'image.required' => 'Изображение упражнения обязательно',
        ];
    }
}
