<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Testing extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration_minutes',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function testExercises(): BelongsToMany
    {
        return $this->belongsToMany(TestingExercise::class, 'testing_test_exercises')
            ->withPivot('order_number')->withTimestamps()->orderBy('order_number');
    }

    public function testingTestExercises(): HasMany
    {
        return $this->hasMany(TestingTestExercise::class);
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'testing_categories')
            ->withTimestamps();
    }
}
