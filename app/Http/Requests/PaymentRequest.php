<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'subscription_id' => 'required|integer|exists:subscriptions,id,is_active,1',
            'save_card' => 'sometimes|boolean',
            'use_saved_card' => 'sometimes|boolean',
        ];

        // Если используется сохраненная карта
        if ($this->input('use_saved_card')) {
            $rules['saved_card_id'] = [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\SavedCard::where('id', $value)
                        ->where('user_id', auth()->id())
                        ->exists();

                    if (!$exists) {
                        $fail('Сохраненная карта не найдена');
                    }
                },
            ];
        } else {
            // Новая карта
            $rules['card_number'] = 'required|string|size:16';
            $rules['card_holder'] = 'required|string|max:255|regex:/^[A-Za-z\s]+$/';
            $rules['expiry_month'] = 'required|string|size:2|in:01,02,03,04,05,06,07,08,09,10,11,12';
            $rules['expiry_year'] = 'required|string|size:4|integer|min:' . date('Y') . '|max:' . (date('Y') + 10);
            $rules['cvv'] = 'required|string|between:3,4';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'subscription_id.required' => 'ID подписки обязателен',
            'subscription_id.exists' => 'Подписка не найдена или неактивна',

            'card_number.required' => 'Номер карты обязателен',
            'card_number.size' => 'Номер карты должен содержать 16 цифр',

            'card_holder.required' => 'Держатель карты обязателен',
            'card_holder.regex' => 'Держатель карты может содержать только латинские буквы и пробелы',

            'expiry_month.required' => 'Месяц истечения обязателен',
            'expiry_month.in' => 'Неверный месяц истечения',

            'expiry_year.required' => 'Год истечения обязателен',
            'expiry_year.min' => 'Год истечения не может быть в прошлом',
            'expiry_year.max' => 'Срок действия карты не может превышать 10 лет',

            'cvv.required' => 'CVV код обязателен',
            'cvv.between' => 'CVV код должен содержать 3 или 4 цифры',
        ];
    }
}
