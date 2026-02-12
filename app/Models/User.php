<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role_id' => $this->role_id,
            'email' => $this->email,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'email_verified' => !is_null($this->email_verified_at)
        ];
    }

    public function generateEmailVerificationCode(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $key = "email_verification:{$this->email}";
        Cache::put($key, $code, 300);
        return $code;
    }

    public function generatePasswordResetCode(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $key = "password_reset:{$this->email}";
        Cache::put($key, $code, 300);
        return $code;
    }

    public function verifyEmailCode(string $code): bool
    {
        $key = "email_verification:{$this->email}";
        $storedCode = Cache::get($key);

        if (!$storedCode) {
            return false;
        }
        return (string) $storedCode === (string) $code;
    }

    public function verifyPasswordResetCode(string $code): bool
    {
        $key = "password_reset:{$this->email}";
        $storedCode = Cache::get($key);

        if (!$storedCode) {
            return false;
        }
        return (string) $storedCode === (string) $code;
    }

    public function clearEmailVerificationCode(): void
    {
        $key = "email_verification:{$this->email}";
        Cache::forget($key);
    }

    public function clearPasswordResetCode(): void
    {
        $key = "password_reset:{$this->email}";
        Cache::forget($key);
    }


    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function userParameters(): HasOne
    {
        return $this->hasOne(UserParameter::class);
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function userWorkouts(): HasMany
    {
        return $this->hasMany(UserWorkout::class);
    }

}
