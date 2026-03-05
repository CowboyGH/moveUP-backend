<?php

namespace App\Http\Requests\Admin\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Изображение обязательно',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif',
            'image.max' => 'Максимальный размер изображения 2MB',
        ];
    }
}
