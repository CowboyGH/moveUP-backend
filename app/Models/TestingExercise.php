<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestingExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'image',
    ];

    public function testings(): BelongsToMany
    {
        return $this->belongsToMany(Testing::class, 'testing_test_exercises')
            ->withPivot('order_number')
            ->withTimestamps();
    }

    public function testingTestExercises(): HasMany
    {
        return $this->hasMany(TestingTestExercise::class);
    }
}
