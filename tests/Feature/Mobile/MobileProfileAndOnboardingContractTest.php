<?php

namespace Tests\Feature\Mobile;

use App\Models\SavedCard;
use App\Models\Subscription;
use App\Models\UserParameter;
use App\Models\UserProgress;
use App\Models\UserSubscription;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Mobile\Support\MobileApiTestCase;

class MobileProfileAndOnboardingContractTest extends MobileApiTestCase
{
    public function test_guest_onboarding_and_authenticated_parameters_contract(): void
    {
        $refs = $this->createReferences();
        $guestHeaders = ['X-Guest-ID' => 'guest-onboarding'];

        $this->getJson('/api/user-parameters/references')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['goals', 'levels', 'equipment']]);

        $this->postJson('/api/user-parameters/goal', ['goal_id' => $refs['goal']->id], $guestHeaders)
            ->assertOk()
            ->assertCookie('guest_id')
            ->assertJsonPath('data.guest_id', 'guest-onboarding')
            ->assertJsonPath('data.guest_data.goal_id', $refs['goal']->id);

        $this->postJson('/api/user-parameters/anthropometry', [
            'gender' => 'male',
            'age' => 30,
            'weight' => 80,
            'height' => 180,
            'equipment_id' => $refs['equipment']->id,
        ], $guestHeaders)
            ->assertOk()
            ->assertJsonPath('data.guest_data.equipment_id', $refs['equipment']->id);

        $this->postJson('/api/user-parameters/level', ['level_id' => $refs['level']->id], $guestHeaders)
            ->assertOk()
            ->assertJsonPath('data.guest_data.level_id', $refs['level']->id);

        $user = $this->createVerifiedUser();
        UserParameter::factory()->create([
            'user_id' => $user->id,
            'goal_id' => $refs['goal']->id,
            'level_id' => $refs['level']->id,
            'equipment_id' => $refs['equipment']->id,
        ]);
        UserProgress::factory()->create([
            'user_id' => $user->id,
            'phase_id' => $refs['phase']->id,
        ]);

        $this->getJson('/api/user-parameters/me', $this->authHeaders($user))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['id', 'user_id', 'goal_id', 'level_id', 'equipment_id']]);

        $this->postJson('/api/user/weekly-goal', ['weekly_goal' => 5], $this->authHeaders($user))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.weekly_goal', 5);
    }

    public function test_profile_avatar_security_details_and_statistics_contract(): void
    {
        $user = $this->createVerifiedUser(['password' => Hash::make('Password123')]);
        $fixtures = $this->createAssignedWorkout($user);
        $subscription = Subscription::factory()->active()->create();

        UserSubscription::factory()->active()->create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
        ]);
        SavedCard::factory()->create(['user_id' => $user->id, 'is_default' => true]);

        $headers = $this->authHeaders($user);

        $this->getJson('/api/profile', $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['user', 'parameters', 'subscriptions', 'workouts', 'tests', 'phase', 'cards']]);

        $this->putJson('/api/profile', ['name' => 'Updated User'], $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Updated User');

        $this->postJson('/api/profile/change-password', [
            'old_password' => 'Password123',
            'new_password' => 'Newpass123',
            'new_password_confirmation' => 'Newpass123',
        ], $headers)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->post('/api/profile/avatar', [
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 256, 256),
        ], $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['avatar_url', 'avatar_path']]);

        $this->deleteJson('/api/profile/avatar', [], $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.avatar_url', null);

        foreach ([
            '/api/profile/user',
            '/api/profile/active-subscription',
            '/api/profile/my-cards',
            '/api/profile/user-parameters',
            '/api/profile/history',
            '/api/profile/phase',
            '/api/profile/statistics',
            '/api/profile/statistics/volume',
            '/api/profile/statistics/trend',
            '/api/profile/statistics/frequency',
            '/api/profile/statistics/exercises',
            '/api/profile/statistics/workouts',
        ] as $endpoint) {
            $this->getJson($endpoint, $headers)
                ->assertOk()
                ->assertJsonPath('success', true);
        }

        $this->deleteJson('/api/profile', [], $headers)
            ->assertOk()
            ->assertJsonPath('success', true);
    }
}
