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
        'testing_id',
        'testing_exercise_id',
        'test_attempt_id',
        'result_value',
        'test_date',
    ];

    protected $casts = [
        'test_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function testing(): BelongsTo
    {
        return $this->belongsTo(Testing::class);
    }
    public function testingExercise(): BelongsTo
    {
        return $this->belongsTo(TestingExercise::class);
    }
    public function testAttempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class);
    }

    public function getExerciseAttribute()
    {
        return $this->testingExercise->exercise ?? null;
    }


}
