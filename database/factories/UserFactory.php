<?php

namespace Database\Factories;

use App\Models\Role;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'role_id' => Role::where('name', 'user')->first()->id,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => fake()->boolean(70) ? now() : null,
            'password' => static::$password ??= Hash::make('Password123'),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::where('name', 'admin')->first()->id,
            'email' => 'admin@moveup.com',
        ]);
    }

    public function user(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::where('name', 'user')->first()->id,
        ]);
    }

    public function withUserParameters(): static
    {
        return $this->has(\App\Models\UserParameter::factory());
    }

    public function withSubscription(): static
    {
        return $this->has(
            \App\Models\UserSubscription::factory()->active()
        );
    }

    public function withProgress(): static
    {
        return $this->has(\App\Models\UserProgress::factory());
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
