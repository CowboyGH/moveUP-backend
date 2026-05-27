<?php

namespace Tests\Feature\Mobile;

use Tests\Feature\Mobile\Support\MobileApiTestCase;

class MobileTestsContractTest extends MobileApiTestCase
{
    public function test_authenticated_test_attempt_contracts(): void
    {
        [$testing, $exercises] = $this->createTestingWithExercises();
        $exercise = $exercises->first();

        $user = $this->createVerifiedUser();
        $headers = $this->authHeaders($user);

        $this->getJson('/api/testings', $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => [['id', 'title', 'exercises_count']]]);

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
