<?php

namespace Tests\Feature\Mobile;

use Tests\Feature\Mobile\Support\MobileApiTestCase;

class MobileTestsContractTest extends MobileApiTestCase
{
    public function test_guest_and_authenticated_test_attempt_contracts(): void
    {
        [$testing, $exercises] = $this->createTestingWithExercises();
        $exercise = $exercises->first();

        $this->getJson('/api/testings')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => [['id', 'title', 'exercises_count']]]);

        $guestHeaders = ['X-Guest-ID' => 'guest-tests'];
        $guestStart = $this->postJson("/api/guest/tests/{$testing->id}/start", [], $guestHeaders)
            ->assertOk()
            ->assertCookie('guest_id')
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['attempt_id', 'current_exercise']]);

        $guestAttemptId = $guestStart->json('data.attempt_id');

        $this->postJson("/api/guest/test-attempts/{$guestAttemptId}/result", [
            'testing_exercise_id' => $exercise->id,
            'result_value' => 3,
        ], $guestHeaders)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.all_exercises_completed', true);

        $this->postJson("/api/guest/test-attempts/{$guestAttemptId}/complete", ['pulse' => 120], $guestHeaders)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['attempt_id', 'completed_at', 'pulse']]);

        $user = $this->createVerifiedUser();
        $headers = $this->authHeaders($user);

        $authStart = $this->postJson("/api/tests/{$testing->id}/start", [], $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['attempt_id', 'current_exercise']]);

        $attemptId = $authStart->json('data.attempt_id');

        $this->postJson("/api/test-attempts/{$attemptId}/result", [
            'testing_exercise_id' => $exercise->id,
            'result_value' => 4,
        ], $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.all_exercises_completed', true);

        $this->postJson("/api/test-attempts/{$attemptId}/complete", ['pulse' => 118], $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['attempt_id', 'completed_at', 'pulse']]);
    }
}
