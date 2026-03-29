<?php

namespace App\Http\Requests\Admin\Warmup;

use App\Http\Requests\ApiFormRequest;

class StoreWarmupRequest extends ApiFormRequest
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Название разминки обязательно',
            'name.max' => 'Название не может быть длиннее 255 символов',
            'description.required' => 'Описание разминки обязательно',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif, webp',
            'image.max' => 'Размер файла не должен превышать 5 МБ',
        ];
    }
}
