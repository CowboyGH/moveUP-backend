<?php

namespace App\Http\Requests\Admin\Exercise;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'equipment_id' => 'sometimes|exists:equipments,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'image' => 'sometimes|string|max:255',
            'muscle_group' => 'sometimes|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'equipment_id.exists' => 'Указанное оборудование не существует',
            'title.max' => 'Название не может быть длиннее 255 символов',
        ];
    }
}
