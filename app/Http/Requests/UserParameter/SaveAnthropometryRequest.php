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
            'age' => 'required|integer|min:14|max:100',
            'weight' => 'required|numeric|min:20|max:300',
            'height' => 'required|integer|min:100|max:250',
            'equipment_id' => 'required|exists:equipments,id',
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
            'age.max' => 'Возраст должен быть не более 100 лет',
            'weight.required' => 'Вес обязателен',
            'weight.numeric' => 'Вес должен быть числом',
            'weight.min' => 'Вес должен быть не менее 20 кг',
            'weight.max' => 'Вес должен быть не более 300 кг',
            'height.required' => 'Рост обязателен',
            'height.integer' => 'Рост должен быть целым числом',
            'height.min' => 'Рост должен быть не менее 100 см',
            'height.max' => 'Рост должен быть не более 250 см',
            'equipment_id.required' => 'ID оборудования обязательно',
            'equipment_id.exists' => 'Выбранное оборудование не существует',
        ];
    }

    public function getData(): array
    {
        return $this->only(['gender', 'age', 'weight', 'height', 'equipment_id']);
    }
}
