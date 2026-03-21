<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\ApiFormRequest;

class BaseFilterRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'sort_by' => 'nullable|string|in:created_at,updated_at,name,title,id',
            'sort_dir' => 'nullable|string|in:asc,desc',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function getPerPage(): int
    {
        // Пагинация по умолчанию = 10
        return $this->input('per_page', 10);
    }

    public function getSortBy(): string
    {
        return $this->input('sort_by', 'created_at');
    }

    public function getSortDir(): string
    {
        return $this->input('sort_dir', 'desc');
    }
}
