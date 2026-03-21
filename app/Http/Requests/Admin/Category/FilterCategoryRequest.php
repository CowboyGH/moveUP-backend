<?php

namespace App\Http\Requests\Admin\Category;

use App\Http\Requests\Admin\BaseFilterRequest;

class FilterCategoryRequest extends BaseFilterRequest
{
    public function rules(): array
    {
        return parent::rules();
    }
}
