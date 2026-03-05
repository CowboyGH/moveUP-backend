<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\PhaseService;
use Illuminate\Console\Command;

class CheckPhasesProgress extends Command
{
    protected $signature = 'phases:check-progress';
    protected $description = 'Проверить прогресс фаз и выполнить автоматические переходы';

    protected PhaseService $phaseService;

    public function __construct(PhaseService $phaseService)
    {
        parent::__construct();
        $this->phaseService = $phaseService;
    }

    public function handle()
    {
        $this->info('Проверка прогресса фаз пользователей...');

        $users = User::whereHas('userProgress')->get();
        $this->info("Найдено пользователей с прогрессом: {$users->count()}");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $stats = [];
        $transitions = 0;
        $transitionDetails = [];

        foreach ($users as $user) {
            $oldPhase = $user->currentProgress()->phase->name ?? 'unknown';
            $oldPhaseId = $user->currentProgress()->phase_id ?? null;

            $this->phaseService->checkAndAdvancePhase($user);

            // Обновляем данные после возможного перехода
            $user->refresh();
            $newPhase = $user->currentProgress()->phase->name ?? 'unknown';
            $newPhaseId = $user->currentProgress()->phase_id ?? null;

            if ($oldPhaseId !== $newPhaseId) {
                $transitions++;
                $transitionDetails[] = [
                    'user_id' => $user->id,
                    'name'    => $user->name,
                    'from'    => $oldPhase,
                    'to'      => $newPhase,
                ];
            }

            $phaseId = $user->currentProgress()->phase_id;
            $stats[$phaseId] = ($stats[$phaseId] ?? 0) + 1;

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($transitions > 0) {
            $this->info("🔁 Произошло переходов фазы: {$transitions}");
            $this->table(['User ID', 'Имя', 'Из фазы', 'В фазу'], $transitionDetails);
        } else {
            $this->info("✅ Переходов не произошло.");
        }

        $this->newLine();
        $this->info("Распределение пользователей по фазам:");
        $phaseNames = \App\Models\Phase::pluck('name', 'id')->toArray();
        $statsWithNames = [];
        foreach ($stats as $phaseId => $count) {
            $statsWithNames[] = [$phaseNames[$phaseId] ?? "Фаза {$phaseId}", $count];
        }
        $this->table(['Фаза', 'Количество'], $statsWithNames);

        return Command::SUCCESS;
    }
}
