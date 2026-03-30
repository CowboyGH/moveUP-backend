<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class TestingExercise extends Model
{
    use HasFactory;
    use Filterable;

    protected $fillable = [
        'title',
        'description',
        'image',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        return Storage::disk('public')->url($this->image);
    }

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

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }
}
