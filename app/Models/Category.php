<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;
    use Filterable;

    protected $fillable = [
        'name',
    ];

    public function testings(): BelongsToMany
    {
        return $this->belongsToMany(Testing::class, 'testing_categories')
            ->withTimestamps();
    }
}
