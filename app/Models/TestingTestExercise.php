<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestingTestExercise extends Model
{
    use HasFactory;

    protected $table = 'testing_test_exercises';

    protected $fillable = [
        'testing_id',
        'testing_exercise_id',
        'order_number',
    ];

    protected $casts = [
        'order_number' => 'integer',
    ];

    public function testing(): BelongsTo
    {
        return $this->belongsTo(Testing::class);
    }

    public function testingExercise(): BelongsTo
    {
        return $this->belongsTo(TestingExercise::class);
    }
}
