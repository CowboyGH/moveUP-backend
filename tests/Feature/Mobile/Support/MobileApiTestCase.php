<?php

namespace Tests\Feature\Mobile\Support;

use App\Models\Equipment;
use App\Models\Exercise;
use App\Models\Goal;
use App\Models\Level;
use App\Models\Phase;
use App\Models\Role;
use App\Models\Testing;
use App\Models\TestingExercise;
use App\Models\User;
use App\Models\UserParameter;
use App\Models\UserProgress;
use App\Models\UserWorkout;
use App\Models\Warmup;
use App\Models\Workout;
use App\Services\GuestDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class MobileApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected InMemoryGuestDataService $guestService;

    protected function setUp(): void
    {
        parent::setUp();

        config(['jwt.secret' => 'mobile-contract-test-secret-32-bytes-ok']);
        Queue::fake();
        Storage::fake('public');

        $this->guestService = new InMemoryGuestDataService();
        $this->app->instance(GuestDataService::class, $this->guestService);
    }

    protected function createVerifiedUser(array $attributes = []): User
    {
        Role::firstOrCreate(['name' => 'user']);

        return User::factory()->create(array_merge([
            'email_verified_at' => now(),
            'password' => Hash::make('Password123'),
        ], $attributes));
    }

    protected function authHeaders(User $user): array
    {
        return [
            'Authorization' => 'Bearer ' . JWTAuth::fromUser($user),
            'Accept' => 'application/json',
        ];
    }

    protected function createReferences(): array
    {
        return [
            'goal' => Goal::factory()->create(),
            'level' => Level::factory()->create(),
            'equipment' => Equipment::factory()->create(),
            'phase' => Phase::factory()->create(['order_number' => 1, 'duration_days' => 7]),
        ];
    }

    protected function createTestingWithExercises(int $count = 1): array
    {
        $testing = Testing::factory()->create(['is_active' => true]);
        $exercises = TestingExercise::factory()->count($count)->create();

        foreach ($exercises as $index => $exercise) {
            $testing->testExercises()->attach($exercise->id, ['order_number' => $index + 1]);
        }

        return [$testing, $exercises];
    }

    protected function createAssignedWorkout(User $user, bool $withWarmup = true): array
    {
        $refs = $this->createReferences();
        $exercise = Exercise::factory()->create(['equipment_id' => $refs['equipment']->id]);
        $workout = Workout::factory()->create(['phase_id' => $refs['phase']->id, 'is_active' => true]);
        $workout->exercises()->attach($exercise->id, [
            'sets' => 3,
            'reps' => 10,
            'order_number' => 1,
        ]);

        $warmup = null;
        if ($withWarmup) {
            $warmup = Warmup::factory()->create();
            $workout->warmups()->attach($warmup->id, ['order_number' => 1]);
        }

        UserParameter::firstOrCreate(
            ['user_id' => $user->id],
            [
                'goal_id' => $refs['goal']->id,
                'level_id' => $refs['level']->id,
                'equipment_id' => $refs['equipment']->id,
                'height' => 180,
                'weight' => 80,
                'age' => 30,
                'gender' => 'male',
            ]
        );

        UserProgress::factory()->create([
            'user_id' => $user->id,
            'phase_id' => $refs['phase']->id,
            'streak_days' => 0,
            'completed_workouts' => 0,
            'weekly_workout_goal' => 4,
        ]);

        $userWorkout = UserWorkout::factory()->assigned()->create([
            'user_id' => $user->id,
            'workout_id' => $workout->id,
        ]);

        return [
            'workout' => $workout,
            'exercise' => $exercise,
            'warmup' => $warmup,
            'user_workout' => $userWorkout,
        ];
    }
}
