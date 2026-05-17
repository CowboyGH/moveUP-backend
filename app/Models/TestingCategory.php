<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestingCategory extends Model
{
    use HasFactory;

    protected $table = 'testing_categories';

    protected $fillable = [
        'testing_id',
        'category_id',
    ];

    public function testing(): BelongsTo
    {
        return $this->belongsTo(Testing::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
