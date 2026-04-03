<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeSearch(Builder $query, ?string $searchTerm, array $fields): Builder
    {
        if (!$searchTerm) {
            return $query;
        }

        return $query->where(function ($q) use ($searchTerm, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%{$searchTerm}%");
            }
        });
    }

    public function scopeDateFilter(Builder $query, ?string $from, ?string $to, string $field = 'created_at'): Builder
    {
        if ($from) {
            $query->whereDate($field, '>=', $from);
        }
        if ($to) {
            $query->whereDate($field, '<=', $to);
        }
        return $query;
    }

    public function scopeStatus(Builder $query, ?bool $isActive, string $field = 'is_active'): Builder
    {
        if ($isActive !== null) {
            $query->where($field, $isActive);
        }
        return $query;
    }
}
