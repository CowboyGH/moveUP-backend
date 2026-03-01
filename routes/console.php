<?php

use App\Console\Commands\ProcessAutoPayments;
use App\Console\Commands\TestPushNotification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('payments:process-auto', function () {
    $this->call(ProcessAutoPayments::class);
})->purpose('Process automatic subscription renewals');

Schedule::command('payments:process-auto')
    ->everyMinute()
    ->withoutOverlapping(5)
    ->appendOutputTo(storage_path('logs/payments.log'));

Schedule::command('phases:check-progress')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/phases.log'));

Artisan::command('push:test', function () {
    $this->call(TestPushNotification::class);
})->purpose('Send test push notification to user(s)');
