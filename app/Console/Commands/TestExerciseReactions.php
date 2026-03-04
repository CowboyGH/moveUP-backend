<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Workout;
use App\Models\UserWorkout;
use App\Models\Exercise;
use App\Services\WorkoutLoadManagerService;
use Illuminate\Console\Command;

class TestExerciseReactions extends Command
{
    protected $signature = 'workouts:test-reactions
                            {user-id : ID пользователя}
                            {workout-id : ID тренировки (из таблицы workouts)}
                            {--reaction=good : Оценка для всех упражнений (good/bad)}
                            {--consecutive=1 : Количество последовательных выполнений}';
    protected $description = 'Имитировать реакции на упражнения для тренировки (автоматически создаёт активную тренировку, если её нет)';

    public function handle(WorkoutLoadManagerService $loadManager)
    {
        $userId = $this->argument('user-id');
        $workoutId = $this->argument('workout-id');
        $reaction = $this->option('reaction');
        $consecutive = (int) $this->option('consecutive');

        $user = User::find($userId);
        if (!$user) {
            $this->error("Пользователь не найден");
            return 1;
        }

        $workout = Workout::with('exercises')->find($workoutId);
        if (!$workout) {
            $this->error("Тренировка с ID {$workoutId} не найдена");
            return 1;
        }

        // Проверяем, есть ли уже активная тренировка у пользователя с этим workout_id
        $userWorkout = UserWorkout::where('user_id', $userId)
            ->where('workout_id', $workoutId)
            ->where('status', 'started')
            ->first();

        if (!$userWorkout) {
            $this->warn("Активная тренировка не найдена. Создаём новую...");
            $userWorkout = UserWorkout::create([
                'user_id' => $userId,
                'workout_id' => $workoutId,
                'status' => 'started',
                'started_at' => now(),
            ]);
            $this->info("Создана тренировка с ID: {$userWorkout->id}");
        } else {
            $this->info("Найдена активная тренировка с ID: {$userWorkout->id}");
        }

        $exercises = $workout->exercises;
        if ($exercises->isEmpty()) {
            $this->error("В тренировке нет упражнений");
            return 1;
        }

        $this->info("Пользователь: {$user->name} (ID: {$userId})");
        $this->info("Тренировка: {$workout->title} (ID: {$workoutId})");
        $this->info("Упражнений в тренировке: {$exercises->count()}");

        // Текущие веса пользователя
        $this->line("Текущие веса пользователя:");
        foreach ($exercises as $ex) {
            $weight = \App\Models\UserExerciseWeight::where('user_id', $userId)
                ->where('exercise_id', $ex->id)
                ->first();
            $current = $weight ? $weight->weight : 'не установлен';
            $this->line("  - {$ex->title}: {$current} кг");
        }

        for ($i = 1; $i <= $consecutive; $i++) {
            $this->newLine();
            $this->info("--- Выполнение #{$i} ---");

            // Формируем данные реакций для всех упражнений
            $reactionsData = [];
            foreach ($exercises as $ex) {
                $reactionsData[] = [
                    'exercise_id' => $ex->id,
                    'reaction'    => $reaction,
                    'performance' => [
                        'sets_completed' => $ex->pivot->sets ?? 3,
                        'reps_completed' => $ex->pivot->reps ?? 12,
                        'weight_used'     => null, // сервис сам подставит текущий вес
                    ],
                ];
            }

            // Вызываем сервис завершения тренировки с реакциями
            $result = $loadManager->completeWorkoutWithLoadAdjustment($user, $workoutId, $reactionsData);

            $this->info("Результаты обработки:");
            foreach ($result['exercise_results'] as $res) {
                $ex = Exercise::find($res['exercise_id']);
                $this->line("  {$ex->title}: реакция {$res['reaction']}");
                if ($res['adjustment_applied']) {
                    $this->line("    → вес изменён: {$res['old_weight']} -> {$res['new_weight']} кг ({$res['adjustment_type']})");
                } else {
                    $this->line("    → вес не изменился");
                }
                if ($res['rest_phase_activated']) {
                    $this->warn("    → рекомендован отдых");
                }
            }

            $this->info("Итоговые веса после выполнения #{$i}:");
            foreach ($result['updated_weights'] as $w) {
                $this->line("  - {$w['exercise_name']}: {$w['current_weight']} кг");
            }

            // Для следующей итерации создаём новую активную тренировку
            if ($i < $consecutive) {
                $newUserWorkout = UserWorkout::create([
                    'user_id' => $userId,
                    'workout_id' => $workoutId,
                    'status' => 'started',
                    'started_at' => now(),
                ]);
                $userWorkout = $newUserWorkout;
                $this->line("Создана новая тренировка ID: {$newUserWorkout->id} для следующего выполнения.");
            }
        }

        return 0;
    }
}
