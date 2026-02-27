<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Phase extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'duration_days',
        'min_workouts',
        'order_number'
    ];



    public function nextPhase(): ?Phase
    {
        return Phase::where('order_number', '>', $this->order_number)
            ->orderBy('order_number')->first();
    }

    public static function getFirstPhase(): ?Phase
    {
        return Phase::orderBy('order_number')->first();
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class);
    }

}
