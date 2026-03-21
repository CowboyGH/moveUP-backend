<?php

namespace App\Http\Requests\Admin\Warmup;

use App\Http\Requests\Admin\BaseFilterRequest;

class FilterWarmupRequest extends BaseFilterRequest
{
    public function rules(): array
    {
        return parent::rules();
    }
}
