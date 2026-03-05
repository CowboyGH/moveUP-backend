<?php

namespace App\Http\Requests\Admin\Workout;

use App\Http\Requests\ApiFormRequest;

class UpdateWorkoutRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phase_id' => 'nullable|exists:phases,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'duration_minutes' => 'sometimes|integer|min:1|max:300',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'sometimes|boolean',
            'exercises' => 'nullable|json',
            'warmups' => 'nullable|json',
        ];
    }

    public function messages(): array
    {
        return [
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif, webp',
            'image.max' => 'Размер файла не должен превышать 5 МБ',
            'exercises.json' => 'Упражнения должны быть в формате JSON',
            'warmups.json' => 'Разминки должны быть в формате JSON',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('exercises') && is_string($this->exercises)) {
            $this->merge([
                'exercises' => json_decode($this->exercises, true)
            ]);
        }

        if ($this->has('warmups') && is_string($this->warmups)) {
            $this->merge([
                'warmups' => json_decode($this->warmups, true)
            ]);
        }
    }
}
