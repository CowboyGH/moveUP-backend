<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
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

    /**
     * Indicate that the model's email address should be unverified.
     */
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
