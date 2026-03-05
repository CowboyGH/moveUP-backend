<?php

namespace App\Http\Requests\Admin\Warmup;

use App\Http\Requests\ApiFormRequest;

class UploadWarmupImageRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Изображение обязательно',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif, webp',
            'image.max' => 'Размер файла не должен превышать 5 МБ',
        ];
    }
}
