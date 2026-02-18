<?php
namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class SaveCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'save_card' => 'required|boolean',
            'card_number' => 'required_if:save_card,true,1,"1","true"|string|size:16',
            'card_holder' => 'required_if:save_card,true,1,"1","true"|string|max:255',
            'expiry_month' => 'required_if:save_card,true,1,"1","true"|string|size:2',
            'expiry_year' => 'required_if:save_card,true,1,"1","true"|string|size:4',
        ];
    }

    public function messages(): array
    {
        return [
            'save_card.required' => 'Поле save_card обязательно',
            'save_card.boolean' => 'Поле save_card должно быть true или false',

            'card_number.required_if' => 'Номер карты обязателен для сохранения',
            'card_number.size' => 'Номер карты должен быть 16 цифр',

            'card_holder.required_if' => 'Имя держателя карты обязательно',

            'expiry_month.required_if' => 'Месяц обязателен',
            'expiry_month.size' => 'Месяц должен быть 2 цифры',

            'expiry_year.required_if' => 'Год обязателен',
            'expiry_year.size' => 'Год должен быть 4 цифры',
        ];
    }

    /**
     * Подготовка данных перед валидацией
     */
    protected function prepareForValidation(): void
    {
        // Приводим save_card к булевому значению для consistent валидации
        if ($this->has('save_card')) {
            $this->merge([
                'save_card' => filter_var($this->save_card, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ]);
        }
    }
}
