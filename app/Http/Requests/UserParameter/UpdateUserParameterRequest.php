<?php

namespace App\Http\Requests\UserParameter;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserParameterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'goal_id' => 'sometimes|exists:goals,id',
            'level_id' => 'sometimes|exists:levels,id',
            'equipment_id' => 'sometimes|exists:equipments,id',
            'height' => 'sometimes|integer|min:140|max:210',
            'weight' => 'sometimes|numeric|min:40|max:130',
            'age' => 'sometimes|integer|min:14|max:90',
            'gender' => 'sometimes|in:male,female',
        ];
    }

    public function messages(): array
    {
        return [
            'goal_id.exists' => 'Выбранная цель не существует',
            'level_id.exists' => 'Выбранный уровень не существует',
            'equipment_id.exists' => 'Выбранное оборудование не существует',
            'height.integer' => 'Рост должен быть целым числом',
            'height.min' => 'Рост должен быть не менее 140 см',
            'height.max' => 'Рост должен быть не более 210 см',
            'weight.numeric' => 'Вес должен быть числом',
            'weight.min' => 'Вес должен быть не менее 40 кг',
            'weight.max' => 'Вес должен быть не более 130 кг',
            'age.integer' => 'Возраст должен быть целым числом',
            'age.min' => 'Возраст должен быть не менее 14 лет',
            'age.max' => 'Возраст должен быть не более 90 лет',
            'gender.in' => 'Пол должен быть male или female',
        ];
    }

    public function getFillableData(): array
    {
        return array_filter($this->only([
            'goal_id',
            'level_id',
            'equipment_id',
            'height',
            'weight',
            'age',
            'gender'
        ]), fn($value) => !is_null($value));
    }
}
