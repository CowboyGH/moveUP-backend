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

class SendVerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        sleep(rand(1, 5));

        try {
            $verificationCode = $this->user->generateEmailVerificationCode();
            Mail::to($this->user->email)->send(new VerificationCodeMail($verificationCode));

            Log::info('Verification email sent to: ' . $this->user->email);
        } catch (\Exception $e) {
            Log::error('Failed to send verification email: ' . $e->getMessage());

            // Если не удалось отправить, пробуем снова через 5 минут
            if ($this->attempts() < 3) {
                $this->release(300);
            }
        }
    }
}
