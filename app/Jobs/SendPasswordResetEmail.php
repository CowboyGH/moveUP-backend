<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\PasswordResetMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendPasswordResetEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(protected User $user) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $resetCode = $this->user->generatePasswordResetCode();
        Mail::to($this->user->email)->send(new PasswordResetMail($resetCode));

        Log::info('Password reset email sent to: ' . $this->user->email);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Failed to send password reset email.', [
            'email' => $this->user->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
