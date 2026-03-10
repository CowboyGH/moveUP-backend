<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Workout extends Model
{
    use HasFactory;
    use Filterable;

    protected $fillable = [
        'phase_id',
        'title',
        'description',
        'duration_minutes',
        'type',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        return Storage::disk('public')->url($this->image);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function userWorkouts(): HasMany
    {
        return $this->hasMany(UserWorkout::class);
    }

    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'workout_exercises')
            ->withPivot('sets', 'reps', 'order_number')
            ->withTimestamps();
    }

    public function warmups(): BelongsToMany
    {
        return $this->belongsToMany(Warmup::class, 'workout_warmups')
            ->withPivot('order_number')
            ->withTimestamps();
    }
}
