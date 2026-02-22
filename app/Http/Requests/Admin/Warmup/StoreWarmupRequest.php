<?php

namespace App\Http\Requests\Admin\Warmup;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarmupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Название разминки обязательно',
            'name.max' => 'Название не может быть длиннее 255 символов',
            'description.required' => 'Описание разминки обязательно',
            'image.required' => 'Изображение разминки обязательно',
        ];
    }
}
