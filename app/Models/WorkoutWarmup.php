<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutWarmup extends Model
{
    use HasFactory;

    protected $table = 'workout_warmups';

    protected $fillable = [
        'workout_id',
        'warmup_id',
        'order_number',
    ];

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function warmup(): BelongsTo
    {
        return $this->belongsTo(Warmup::class);
    }
}
