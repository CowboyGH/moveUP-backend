<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => 'sometimes|string|max:255|unique:categories,name,' . $id,
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Название не может быть длиннее 255 символов',
            'name.unique' => 'Категория с таким названием уже существует',
        ];
    }
}
