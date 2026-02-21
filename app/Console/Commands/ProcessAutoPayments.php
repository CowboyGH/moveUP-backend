<?php

namespace App\Console\Commands;

use App\Services\Payment\AutoPaymentService;
use Illuminate\Console\Command;

class ProcessAutoPayments extends Command
{
    protected $signature = 'payments:process-auto';
    protected $description = 'Process automatic subscription renewals';

    protected $autoPaymentService;

    public function __construct(AutoPaymentService $autoPaymentService)
    {
        parent::__construct();
        $this->autoPaymentService = $autoPaymentService;
    }

    public function handle()
    {
        $this->info('Starting auto-payment processing...');

        try {
            $this->autoPaymentService->processExpiringSubscriptions();
            $this->info('Auto-payment processing completed successfully.');
        } catch (\Exception $e) {
            $this->error('Error processing auto-payments: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
