<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestingExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'testing_id',
        'description',
        'image',
    ];

    public function testing(): BelongsTo
    {
        return $this->belongsTo(Testing::class);
    }
}
