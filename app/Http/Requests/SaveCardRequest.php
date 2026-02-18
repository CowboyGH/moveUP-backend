<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'save_card' => 'required|boolean',
        ];

        // Если save_card = true, добавляем правила для карты
        if ($this->input('save_card') == '1' || $this->input('save_card') === true || $this->input('save_card') === 'true') {
            $rules['card_number'] = 'required|string|size:16';
            $rules['card_holder'] = 'required|string';
            $rules['expiry_month'] = 'required|string|size:2';
            $rules['expiry_year'] = 'required|string|size:4';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'save_card.required' => 'Поле save_card обязательно',
            'save_card.boolean' => 'Поле save_card должно быть true или false',

            'card_number.required' => 'Номер карты обязателен',
            'card_number.size' => 'Номер карты должен быть 16 цифр',

            'card_holder.required' => 'Имя держателя карты обязательно',

            'expiry_month.required' => 'Месяц обязателен',
            'expiry_month.size' => 'Месяц должен быть 2 цифры',

            'expiry_year.required' => 'Год обязателен',
            'expiry_year.size' => 'Год должен быть 4 цифры',
        ];
    }
}
