<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\PhaseService;
use Illuminate\Console\Command;

class CheckPhasesProgress extends Command
{
    protected $signature = 'phases:check-progress';
    protected $description = 'Check and update phases progress for all users';

    protected $phaseService;

    public function __construct(PhaseService $phaseService)
    {
        parent::__construct();
        $this->phaseService = $phaseService;
    }

    public function handle()
    {
        $this->info('Checking phases progress for all users...');

        $users = User::whereHas('userProgress')->get();
        $bar = $this->output->createProgressBar($users->count());

        foreach ($users as $user) {
            $this->phaseService->checkAndAdvancePhase($user);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done!');
    }
}
