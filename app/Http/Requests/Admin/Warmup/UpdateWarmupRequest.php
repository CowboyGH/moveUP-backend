<?php

namespace App\Http\Requests\Admin\Warmup;

use App\Http\Requests\ApiFormRequest;

class UpdateWarmupRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Название не может быть длиннее 255 символов',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif, webp',
            'image.max' => 'Размер файла не должен превышать 5 МБ',
        ];
    }
}
