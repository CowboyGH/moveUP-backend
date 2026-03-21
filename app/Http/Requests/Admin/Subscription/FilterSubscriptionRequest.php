<?php

namespace App\Http\Requests\Admin\Subscription;

use App\Http\Requests\Admin\BaseFilterRequest;

class FilterSubscriptionRequest extends BaseFilterRequest
{
    public function rules(): array
    {
        return parent::rules();
    }

    public function messages(): array
    {
        return [
            'duration_days.in' => 'Длительность должна быть 30, 90, 180 или 365 дней',
        ];
    }
}
