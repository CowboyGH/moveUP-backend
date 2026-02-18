<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'subscription_id' => 'required|integer|exists:subscriptions,id,is_active,1',
            'save_card' => 'required|boolean',
            'use_saved_card' => 'required|boolean',
        ];

        // Если используем сохраненную карту
        if ($this->input('use_saved_card') == '1' || $this->input('use_saved_card') === true || $this->input('use_saved_card') === 'true') {
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
        }
        // Если используем новую карту
        else {
            $rules['card_number'] = 'required|string|size:16';
            $rules['card_holder'] = 'required|string';
            $rules['expiry_month'] = 'required|string|size:2';
            $rules['expiry_year'] = 'required|string|size:4';

            // CVV не сохраняем, но для оплаты нужен
            $rules['cvv'] = 'required|string|between:3,4';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'subscription_id.required' => 'Выберите подписку',
            'subscription_id.exists' => 'Подписка не найдена',

            'save_card.required' => 'Поле save_card обязательно',
            'save_card.boolean' => 'Поле save_card должно быть true или false',

            'use_saved_card.required' => 'Поле use_saved_card обязательно',
            'use_saved_card.boolean' => 'Поле use_saved_card должно быть true или false',

            'saved_card_id.required' => 'Выберите сохраненную карту',

            'card_number.required' => 'Номер карты обязателен',
            'card_number.size' => 'Номер карты должен быть 16 цифр',

            'card_holder.required' => 'Имя держателя карты обязательно',

            'expiry_month.required' => 'Месяц обязателен',
            'expiry_month.size' => 'Месяц должен быть 2 цифры',

            'expiry_year.required' => 'Год обязателен',
            'expiry_year.size' => 'Год должен быть 4 цифры',

            'cvv.required' => 'CVV код обязателен',
            'cvv.between' => 'CVV код должен быть 3 или 4 цифры',
        ];
    }
}
