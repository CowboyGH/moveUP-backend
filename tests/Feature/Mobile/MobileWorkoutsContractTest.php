<?php

namespace Tests\Feature\Mobile;

use App\Models\UserWorkout;
use Tests\Feature\Mobile\Support\MobileApiTestCase;

class MobileWorkoutsContractTest extends MobileApiTestCase
{
    public function test_workout_overview_execution_completion_and_abandon_contract(): void
    {
        $user = $this->createVerifiedUser();
        $fixtures = $this->createAssignedWorkout($user);
        $headers = $this->authHeaders($user);

        $this->getJson('/api/workouts', $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['assigned', 'started', 'has_active']]);

        $start = $this->postJson('/api/workouts/start', [
            'workout_id' => $fixtures['workout']->id,
            'with_warmup' => false,
        ], $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['user_workout_id', 'type', 'exercise']]);

        $userWorkoutId = $start->json('data.user_workout_id');

        $this->getJson("/api/workout-execution/{$userWorkoutId}", $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['user_workout_id', 'workout', 'exercises', 'status']]);

        $this->postJson("/api/workout-execution/{$userWorkoutId}/save-exercise-result", [
            'exercise_id' => $fixtures['exercise']->id,
            'reaction' => 'good',
            'weight_used' => 40,
            'sets_completed' => 3,
            'reps_completed' => 10,
        ], $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.all_exercises_completed', true);

        $this->postJson("/api/workout-execution/{$userWorkoutId}/complete", [], $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['user_workout', 'phase_progress']]);

        $second = $this->createAssignedWorkout($user);

        $this->postJson('/api/workouts/start', [
            'workout_id' => $second['workout']->id,
            'with_warmup' => true,
        ], $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['user_workout_id', 'type', 'warmup']]);

        $this->postJson("/api/workouts/{$second['user_workout']->id}/abandon", [], $headers)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame(
            UserWorkout::STATUS_ASSIGNED,
            $second['user_workout']->fresh()->status
        );
    }
}
