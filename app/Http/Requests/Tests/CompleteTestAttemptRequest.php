<?php

namespace App\Http\Requests\Tests;

use App\Http\Requests\ApiFormRequest;

class CompleteTestAttemptRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pulse' => 'required|integer|min:30|max:220',
        ];
    }
}
