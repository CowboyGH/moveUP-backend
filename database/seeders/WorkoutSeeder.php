<?php

namespace Database\Seeders;

use App\Models\Phase;
use App\Models\Workout;
use Illuminate\Database\Seeder;

class WorkoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phases = Phase::all();

        if ($phases->isEmpty()) {
            $this->call(PhaseSeeder::class);
            $phases = Phase::all();
        }

        // Для каждой фазы создаем тренировки разных типов
        foreach ($phases as $phase) {
            $this->createWorkoutsForPhase($phase);
        }
    }

    private function createWorkoutsForPhase(Phase $phase): void
    {
        // Определяем типы тренировок в зависимости от фазы
        $workoutConfigs = $this->getWorkoutConfigsForPhase($phase);

        foreach ($workoutConfigs as $config) {
            Workout::factory()
                ->forPhase($phase)
                ->ofType($config['type'])
                ->count($config['count'] ?? 1)
                ->create();
        }
    }

    private function getWorkoutConfigsForPhase(Phase $phase): array
    {
        return match($phase->order_number) {
            1 => [
                ['type' => 'general', 'count' => 3],
                ['type' => 'strength', 'count' => 2],
                ['type' => 'cardio', 'count' => 2],
            ],
            2 => [
                ['type' => 'strength', 'count' => 4],
                ['type' => 'hypertrophy', 'count' => 3],
                ['type' => 'cardio', 'count' => 3],
                ['type' => 'circuit', 'count' => 2],
            ],
            3 => [
                ['type' => 'strength', 'count' => 5],
                ['type' => 'power', 'count' => 3],
                ['type' => 'hiit', 'count' => 4],
                ['type' => 'circuit', 'count' => 3],
                ['type' => 'functional', 'count' => 3],
            ],
            4 => [
                ['type' => 'general', 'count' => 3],
                ['type' => 'cardio', 'count' => 2],
                ['type' => 'functional', 'count' => 2],
            ],
            5 => [
                ['type' => 'strength', 'count' => 6],
                ['type' => 'power', 'count' => 4],
                ['type' => 'hypertrophy', 'count' => 5],
                ['type' => 'hiit', 'count' => 5],
                ['type' => 'functional', 'count' => 4],
                ['type' => 'circuit', 'count' => 4],
            ],
            default => [
                ['type' => 'general', 'count' => 5],
            ],
        };
    }
}
