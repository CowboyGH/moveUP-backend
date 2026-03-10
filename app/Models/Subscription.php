<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;
    use Filterable;

    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
        'duration_days',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function getImageAttribute($value)
    {
        if ($value) {
            return asset('storage/' . $value);
        }
        return asset('images/default-subscription.jpg');
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }
}
