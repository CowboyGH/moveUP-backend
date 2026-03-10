<?php

namespace App\Http\Requests\Admin\Subscription;

use App\Http\Requests\Admin\BaseFilterRequest;

class FilterSubscriptionRequest extends BaseFilterRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0|gte:price_min',
            'duration_days' => 'nullable|integer|in:30,90,180,365',
        ]);
    }

    public function messages(): array
    {
        return [
            'duration_days.in' => 'Длительность должна быть 30, 90, 180 или 365 дней',
        ];
    }
}
