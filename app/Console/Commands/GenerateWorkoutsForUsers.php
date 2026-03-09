<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\PhaseService;
use App\Services\WorkoutGeneration\WorkoutGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateWorkoutsForUsers extends Command
{
    protected $signature = 'workouts:generate-for-all-users
                            {--user-id= : ID конкретного пользователя}
                            {--force : Принудительная генерация даже если есть тренировки}
                            {--show-details : Показать детали сгенерированных тренировок}';

    protected $description = 'Генерация тренировок для пользователей';

    protected PhaseService $phaseService;
    protected WorkoutGeneratorService $generator;

    public function __construct(PhaseService $phaseService, WorkoutGeneratorService $generator)
    {
        parent::__construct();
        $this->phaseService = $phaseService;
        $this->generator = $generator;
    }

    public function handle()
    {
        $userId = $this->option('user-id');
        $force = $this->option('force');
        $showDetails = $this->option('show-details');

        $this->info('Начинаем генерацию тренировок...');
        Log::info('Запущена команда генерации тренировок', ['force' => $force]);

        if ($userId) {
            $user = User::with('userParameters')->find($userId);
            if ($user) {
                $this->generateForUser($user, $force, $showDetails);
            } else {
                $this->error("Пользователь с ID {$userId} не найден");
            }
        } else {
            $users = User::whereHas('userParameters')->get();
            $this->info("Найдено пользователей с параметрами: {$users->count()}");

            $bar = $this->output->createProgressBar($users->count());
            $bar->start();

            $generated = 0;
            $skipped = 0;
            $errors = 0;

            foreach ($users as $user) {
                try {
                    $result = $this->generateForUser($user, $force, false); // без деталей в цикле
                    if ($result) {
                        $generated++;
                    } else {
                        $skipped++;
                    }
                } catch (\Exception $e) {
                    $errors++;
                    Log::error("Ошибка генерации для пользователя {$user->id}: " . $e->getMessage());
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            $this->table(
                ['Результат', 'Количество'],
                [
                    ['Сгенерировано', $generated],
                    ['Пропущено', $skipped],
                    ['Ошибок', $errors],
                ]
            );
        }

        Log::info('Команда генерации тренировок завершена');
        $this->info('Готово!');
        return Command::SUCCESS;
    }

    private function generateForUser(User $user, bool $force, bool $showDetails): bool
    {
        if (!$user->userParameters) {
            $this->warn("У пользователя {$user->id} нет параметров");
            return false;
        }

        $params = $user->userParameters;
        if (!$params->goal_id || !$params->level_id || !$params->equipment_id) {
            $this->warn("У пользователя {$user->id} не все параметры заполнены");
            return false;
        }

        $currentProgress = $user->currentProgress();
        if (!$currentProgress) {
            $currentProgress = $this->phaseService->assignInitialPhase($user);
            $this->info("Создана начальная фаза для пользователя {$user->id}");
        }

        $hasActiveWorkouts = $user->userWorkouts()->where('status', 'started')->exists();

        if ($hasActiveWorkouts && !$force) {
            $this->line("У пользователя {$user->id} уже есть активные тренировки (пропускаем)");
            return false;
        }

        if ($force && $hasActiveWorkouts) {
            $deleted = $user->userWorkouts()->where('status', 'started')->delete();
            $this->line("Удалено {$deleted} старых тренировок пользователя {$user->id}");
        }

        $workouts = $this->generator->generateForPhase($user, $currentProgress->phase);

        if ($workouts->isEmpty()) {
            $this->warn("Не удалось сгенерировать тренировки для пользователя {$user->id}");
            return false;
        }

        $this->generator->assignWorkoutsToUser($user, $workouts);

        $this->info("Сгенерировано {$workouts->count()} тренировок для пользователя {$user->id} ({$user->name})");

        if ($showDetails) {
            $this->line("  Тренировки:");
            foreach ($workouts as $index => $workout) {
                $this->line("    " . ($index+1) . ". {$workout->title} ({$workout->type})");
                // Покажем первое упражнение для примера
                $firstExercise = $workout->exercises->first();
                if ($firstExercise) {
                    $sets = $firstExercise->pivot->sets;
                    $reps = $firstExercise->pivot->reps;
                    $this->line("       → Например: {$firstExercise->title} – {$sets} x {$reps}");
                }
            }
        }

        Log::info("Сгенерировано тренировок", [
            'user_id' => $user->id,
            'phase_id' => $currentProgress->phase->id,
            'count' => $workouts->count()
        ]);

        return true;
    }
}
