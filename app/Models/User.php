<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'email_verification_code',
        'email_verification_code_expires_at',
        'password_reset_code',
        'password_reset_code_expires_at'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_code',
        'password_reset_code'
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
            'email_verification_code_expires_at' => 'datetime',
            'password_reset_code_expires_at' => 'datetime'
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

        $this->update([
            'email_verification_code' => $code,
            'email_verification_code_expires_at' => now()->addMinutes(5)
        ]);

        return $code;
    }

    public function generatePasswordResetCode(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'password_reset_code' => $code,
            'password_reset_code_expires_at' => now()->addMinutes(5)
        ]);

        return $code;
    }

    public function verifyEmailCode(string $code): bool
    {
        $storedCode = (string) $this->email_verification_code;
        $inputCode = (string) $code;

        $storedCode = trim($storedCode);
        $inputCode = trim($inputCode);

        return $storedCode === $inputCode
            && $this->email_verification_code_expires_at
            && $this->email_verification_code_expires_at->isFuture();
    }

    public function verifyPasswordResetCode(string $code): bool
    {
        $storedCode = (string) $this->password_reset_code;
        $inputCode = (string) $code;

        $storedCode = trim($storedCode);
        $inputCode = trim($inputCode);

        return $storedCode === $inputCode
            && $this->password_reset_code_expires_at
            && $this->password_reset_code_expires_at->isFuture();
    }

    public function clearEmailVerificationCode(): void
    {
        $this->update([
            'email_verification_code' => null,
            'email_verification_code_expires_at' => null
        ]);
    }

    public function clearPasswordResetCode(): void
    {
        $this->update([
            'password_reset_code' => null,
            'password_reset_code_expires_at' => null
        ]);
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
