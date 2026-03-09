<?php

namespace App\Console\Commands;

use App\Models\Phase;
use App\Models\User;
use App\Services\WorkoutGeneration\WorkoutGeneratorService;
use Illuminate\Console\Command;

class CheckAllPhases extends Command
{
    protected $signature = 'workouts:check-phases {user-id}';
    protected $description = 'Проверить генерацию тренировок для всех фаз (без сохранения)';

    public function handle(WorkoutGeneratorService $generator)
    {
        $userId = $this->argument('user-id');
        $user = User::with('userParameters.goal', 'userParameters.level')->find($userId);

        if (!$user) {
            $this->error("Пользователь не найден");
            return 1;
        }

        // Получаем недельную цель пользователя (из прогресса или по умолчанию 4)
        $currentProgress = $user->currentProgress();
        $weeklyGoal = $currentProgress->weekly_workout_goal ?? 4;

        $phases = Phase::orderBy('order_number')->get();

        $this->info("🧪 Проверка генерации для пользователя ID: {$userId} – {$user->name}");
        $this->info("📊 Параметры пользователя:");
        $this->line("  Цель: " . ($user->userParameters->goal->name ?? 'Не указана'));
        $this->line("  Уровень: " . ($user->userParameters->level->name ?? 'Не указан'));
        $this->line("  Оборудование ID: " . ($user->userParameters->equipment_id ?? 'Не указано'));
        $this->line("  Недельная цель тренировок: {$weeklyGoal}");
        $this->newLine();

        $tableData = [];

        foreach ($phases as $phase) {
            $this->info("📌 Фаза {$phase->order_number}: {$phase->name}");
            $expectedTotal = (int) ceil($phase->duration_days / 7 * $weeklyGoal);
            $this->line("  Длительность: {$phase->duration_days} дней, ожидаемое кол-во тренировок за фазу: {$expectedTotal}");

            // Генерация (без сохранения)
            $workouts = $generator->generateForPhase($user, $phase);

            if ($workouts->isEmpty()) {
                $this->warn("  ⚠️ Нет тренировок для этой фазы");
                $tableData[] = [
                    $phase->order_number,
                    $phase->name,
                    '0/' . $phase->duration_days,
                    $expectedTotal,
                    '-',
                    '-'
                ];
                $this->newLine();
                continue;
            }

            // Статистика по типам
            $types = [];
            $totalExercises = 0;
            $totalAlternatives = 0;

            foreach ($workouts as $w) {
                $type = $w->type ?? 'general';
                $types[$type] = ($types[$type] ?? 0) + 1;
                foreach ($w->exercises as $ex) {
                    $totalExercises++;
                    if ($ex->is_alternative ?? false) $totalAlternatives++;
                }
            }

            $this->info("  ✅ Сгенерировано: {$workouts->count()} тренировок");
            $this->line("  📊 По типам:");
            foreach ($types as $type => $count) {
                $this->line("    - {$type}: {$count}");
            }

            // Детали по первой тренировке
            $first = $workouts->first();
            $this->line("  🏋️ Пример тренировки: {$first->title}");
            $firstExercise = $first->exercises->first();
            if ($firstExercise) {
                $sets = $firstExercise->pivot->sets;
                $reps = $firstExercise->pivot->reps;
                $alt = isset($firstExercise->is_alternative) ? ' (замена)' : '';
                $this->line("    → Упражнение: {$firstExercise->title}{$alt} – {$sets} x {$reps}");
                if (isset($firstExercise->user_weight)) {
                    $this->line("    → Вес пользователя: {$firstExercise->user_weight} кг");
                }
            }

            // Адаптация статистика
            $adaptPercent = $totalExercises > 0 ? round(($totalAlternatives / $totalExercises) * 100) : 0;
            $this->line("  🔄 Заменено упражнений: {$totalAlternatives} из {$totalExercises} ({$adaptPercent}%)");

            $tableData[] = [
                $phase->order_number,
                $phase->name,
                $workouts->count() . '/' . $phase->duration_days,
                $expectedTotal,
                implode(', ', array_map(fn($t, $c) => "$t:$c", array_keys($types), $types)),
                "{$adaptPercent}%"
            ];

            $this->newLine();
        }

        $this->table(
            ['Фаза', 'Название', 'Сгенер/Дней', 'Ожид всего', 'Типы', 'Замен'],
            $tableData
        );

        return 0;
    }
}
