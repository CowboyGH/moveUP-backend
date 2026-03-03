<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Phase;
use App\Services\WorkoutGeneratorService;
use Illuminate\Console\Command;

class CheckAllPhases extends Command
{
    protected $signature = 'workouts:check-phases {user-id}';
    protected $description = 'Проверить генерацию тренировок для всех фаз';

    public function handle(WorkoutGeneratorService $generator)
    {
        $userId = $this->argument('user-id');
        $user = User::with('userParameters')->find($userId);

        if (!$user) {
            $this->error("Пользователь не найден");
            return 1;
        }

        $phases = Phase::orderBy('order_number')->get();

        $this->info("Проверка генерации для пользователя ID: {$userId}");
        $this->info("Параметры пользователя:");
        $this->line("  Цель: " . ($user->userParameters->goal->name ?? 'Не указана'));
        $this->line("  Уровень: " . ($user->userParameters->level->name ?? 'Не указан'));
        $this->newLine();

        $tableData = [];

        foreach ($phases as $phase) {
            $this->info("Фаза {$phase->order_number}: {$phase->name}");
            $this->line("  Длительность: {$phase->duration_days} дней, минимум тренировок: {$phase->min_workouts}");

            $workouts = $generator->generateForPhase($user, $phase);

            if ($workouts->isEmpty()) {
                $this->warn("Нет тренировок для этой фазы");
                continue;
            }
            $types = [];
            foreach ($workouts as $w) {
                $type = $w->type ?? 'general';
                $types[$type] = ($types[$type] ?? 0) + 1;
            }

            $this->info("Сгенерировано: {$workouts->count()} тренировок");
            $this->line("По типам:");
            foreach ($types as $type => $count) {
                $this->line("    - {$type}: {$count}");
            }

            $this->line("Примеры:");
            foreach ($workouts->take(3) as $w) {
                $this->line("    • {$w->title}");
            }

            $tableData[] = [
                $phase->order_number,
                $phase->name,
                $workouts->count() . '/' . $phase->duration_days,
                implode(', ', array_map(fn($t, $c) => "$t:$c", array_keys($types), $types)),
            ];

            $this->newLine();
        }

        $this->table(
            ['Фаза', 'Название', 'Тренировок/Дней', 'Типы'],
            $tableData
        );

        return 0;
    }
}
