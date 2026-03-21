<?php

namespace App\Http\Requests\Admin\Testing;

use App\Http\Requests\Admin\BaseFilterRequest;

class FilterTestingRequest extends BaseFilterRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'category_id' => 'nullable|integer|exists:categories,id',
        ]);
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Выбранная категория не существует',
        ];
    }
}
