<?php
namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'card_number' => 'required|string|size:16',
            'card_holder' => 'required|string|max:50',
            'expiry_month' => [
                'required',
                'string',
                'size:2',
                Rule::in(['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12']),
            ],
            'expiry_year' => 'required|string|size:4',
        ];
    }

    public function messages(): array
    {
        return [
            'card_number.required' => 'Номер карты обязателен',
            'card_number.size' => 'Номер карты должен быть 16 цифр',

            'card_holder.required' => 'Имя держателя карты обязательно',

            'expiry_month.required' => 'Месяц обязателен',
            'expiry_month.size' => 'Месяц должен быть 2 цифры',
            'expiry_month.in' => 'Месяц должен быть от 01 до 12',

            'expiry_year.required' => 'Год обязателен',
            'expiry_year.size' => 'Год должен быть 4 цифры',
        ];
    }
}
