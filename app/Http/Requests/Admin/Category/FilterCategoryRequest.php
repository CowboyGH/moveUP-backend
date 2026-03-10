<?php

namespace App\Http\Requests\Admin\Category;

use App\Http\Requests\Admin\BaseFilterRequest;

class FilterCategoryRequest extends BaseFilterRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            // Специфичные для категорий правила
        ]);
    }
}
