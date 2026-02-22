<?php

namespace App\Http\Requests\Admin\Warmup;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarmupRequest extends FormRequest
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
            'image' => 'sometimes|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Название не может быть длиннее 255 символов',
        ];
    }
}
