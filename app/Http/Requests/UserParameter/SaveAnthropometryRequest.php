<?php

namespace App\Http\Requests\UserParameter;

use Illuminate\Foundation\Http\FormRequest;

class SaveAnthropometryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gender' => 'required|in:male,female',
            'age' => 'required|integer|min:14|max:90',
            'weight' => 'required|numeric|min:40|max:130',
            'height' => 'required|integer|min:140|max:210',
            'equipment_id' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'gender.required' => 'Пол обязателен',
            'gender.in' => 'Пол должен быть male или female',
            'age.required' => 'Возраст обязателен',
            'age.integer' => 'Возраст должен быть целым числом',
            'age.min' => 'Возраст должен быть не менее 14 лет',
            'age.max' => 'Возраст должен быть не более 90 лет',
            'weight.required' => 'Вес обязателен',
            'weight.numeric' => 'Вес должен быть числом',
            'weight.min' => 'Вес должен быть не менее 40 кг',
            'weight.max' => 'Вес должен быть не более 130 кг',
            'height.required' => 'Рост обязателен',
            'height.integer' => 'Рост должен быть целым числом',
            'height.min' => 'Рост должен быть не менее 140 см',
            'height.max' => 'Рост должен быть не более 210 см',
            'equipment_id.required' => 'ID оборудования обязательно',
            'equipment_id.integer' => 'ID оборудования должен быть целым числом',
            'equipment_id.min' => 'ID оборудования должен быть больше 0',
        ];
    }

    public function getData(): array
    {
        return $this->only(['gender', 'age', 'weight', 'height', 'equipment_id']);
    }
}
