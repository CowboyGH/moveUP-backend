<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exercise_id',
        'testing_id',
        'result_value',
        'pulse',
        'test_date',
    ];

    protected $casts = [
        'test_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function testing(): BelongsTo
    {
        return $this->belongsTo(Testing::class);
    }
}
