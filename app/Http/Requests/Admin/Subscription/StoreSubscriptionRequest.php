<?php

namespace App\Http\Requests\Admin\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|in:30,90,180,365',
            'is_active' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Название подписки обязательно',
            'name.max' => 'Название не может быть длиннее 255 символов',
            'description.required' => 'Описание подписки обязательно',
            'price.required' => 'Цена обязательна',
            'price.min' => 'Цена не может быть отрицательной',
            'duration_days.required' => 'Длительность подписки обязательна',
            'duration_days.in' => 'Длительность должна быть 30, 90, 180 или 365 дней',
        ];
    }
}
