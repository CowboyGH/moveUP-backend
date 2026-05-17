<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\VerificationCodeMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendVerificationEmail implements ShouldQueue
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
        $verificationCode = $this->user->generateEmailVerificationCode();
        Mail::to($this->user->email)->send(new VerificationCodeMail($verificationCode));

        Log::info('Verification email sent to: ' . $this->user->email);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Failed to send verification email.', [
            'email' => $this->user->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
