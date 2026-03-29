<?php

namespace App\Http\Requests\Admin\Exercise;

use App\Http\Requests\ApiFormRequest;

class StoreExerciseRequest extends ApiFormRequest
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
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
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif, webp',
            'image.max' => 'Размер файла не должен превышать 5 МБ',
            'muscle_group.required' => 'Группа мышц обязательна',
        ];
    }
}
