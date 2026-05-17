<?php

namespace Tests\Feature\Mobile;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Mobile\Support\MobileApiTestCase;

class MobileAuthContractTest extends MobileApiTestCase
{
    public function test_auth_lifecycle_preserves_mobile_contract(): void
    {
        Role::query()->where('name', 'user')->delete();

        $payload = [
            'name' => 'Mobile User',
            'email' => 'mobile@example.com',
            'password' => 'Password123',
        ];

        $this->postJson('/api/register', $payload, ['X-Guest-ID' => 'guest-auth'])
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('user.role_id', Role::where('name', 'user')->first()->id)
            ->assertJsonStructure([
                'success',
                'message',
                'user' => ['id', 'name', 'email', 'role_id'],
            ]);

        $user = User::where('email', $payload['email'])->firstOrFail();
        $code = $user->generateEmailVerificationCode();

        $this->postJson('/api/verify-email', [
            'email' => $payload['email'],
            'code' => $code,
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                    'user' => ['id', 'email', 'role_id'],
                ],
            ]);

        $login = $this->postJson('/api/login', [
            'email' => $payload['email'],
            'password' => $payload['password'],
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('token_type', 'bearer')
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'refresh_expires_in',
                'session' => ['lifetime_days', 'inactivity_limit_days', 'access_token_expires_in_minutes'],
                'user' => ['id', 'email', 'role_id'],
            ]);

        $token = $login->json('access_token');

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['user' => ['id', 'email', 'role_id']]);

        $refresh = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/refresh')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('token_type', 'bearer')
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);

        $this->withHeader('Authorization', 'Bearer ' . $refresh->json('access_token'))
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_auth_error_codes_and_password_reset_contract(): void
    {
        $this->getJson('/api/me')
            ->assertUnauthorized()
            ->assertJsonPath('code', 'unauthorized');

        $user = $this->createVerifiedUser([
            'email' => 'reset@example.com',
            'password' => Hash::make('Password123'),
        ]);

        $this->postJson('/api/forgot-password', ['email' => $user->email])
            ->assertOk()
            ->assertJsonPath('success', true);

        $code = $user->generatePasswordResetCode();

        $this->postJson('/api/resend-reset-code', ['email' => $user->email])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->postJson('/api/verify-reset-code', [
            'email' => $user->email,
            'code' => $code,
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'code' => $code,
            'password' => 'Newpass123',
            'password_confirmation' => 'Newpass123',
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'Newpass123',
        ])
            ->assertOk()
            ->assertJsonStructure(['access_token', 'user' => ['role_id']]);
    }
}
