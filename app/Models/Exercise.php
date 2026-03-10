<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Exercise extends Model
{
    use HasFactory;
    use Filterable;

    protected $fillable = [
        'equipment_id',
        'title',
        'description',
        'image',
        'muscle_group',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        return Storage::disk('public')->url($this->image);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    public function exercisePerformances(): HasMany
    {
        return $this->hasMany(ExercisePerformance::class);
    }

    public function workouts(): BelongsToMany
    {
        return $this->belongsToMany(Workout::class, 'workout_exercises')
            ->withPivot('sets', 'reps', 'order_number')
            ->withTimestamps();
    }
}
