<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiFormRequest;

class UpdateAvatarRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'avatar' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120',
                'dimensions:min_width=100,min_height=100,max_width=2048,max_height=2048'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Необходимо выбрать изображение',
            'avatar.image' => 'Файл должен быть изображением',
            'avatar.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif, webp',
            'avatar.max' => 'Размер файла не должен превышать 5 МБ',
            'avatar.dimensions' => 'Изображение должно быть от 100x100 до 2048x2048 пикселей',
        ];
    }
}
