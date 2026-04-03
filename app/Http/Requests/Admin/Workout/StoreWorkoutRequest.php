<?php

namespace App\Http\Requests\Admin\Workout;

use App\Http\Requests\ApiFormRequest;

class StoreWorkoutRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phase_id' => 'nullable|exists:phases,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1|max:300',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'boolean',
            'exercises' => 'nullable|array',
            'warmups' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название тренировки обязательно',
            'title.max' => 'Название не может превышать 255 символов',
            'description.required' => 'Описание тренировки обязательно',
            'duration_minutes.required' => 'Длительность тренировки обязательна',
            'duration_minutes.min' => 'Длительность должна быть не менее 1 минуты',
            'duration_minutes.max' => 'Длительность не может превышать 300 минут',
            'phase_id.exists' => 'Выбранная фаза не существует',
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
