<?php

namespace App\Http\Requests\Admin\Exercise;

use Illuminate\Foundation\Http\FormRequest;

class StoreExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'equipment_id' => 'required|exists:equipments,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|string|max:255',
            'muscle_group' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'equipment_id.required' => 'ID оборудования обязательно',
            'equipment_id.exists' => 'Указанное оборудование не существует',
            'title.required' => 'Название упражнения обязательно',
            'title.max' => 'Название не может быть длиннее 255 символов',
            'description.required' => 'Описание упражнения обязательно',
            'image.required' => 'Изображение упражнения обязательно',
            'muscle_group.required' => 'Группа мышц обязательна',
        ];
    }
}
