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

class SendPasswordResetEmail implements ShouldQueue
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
            $resetCode = $this->user->generatePasswordResetCode();
            Mail::to($this->user->email)->send(new PasswordResetMail($resetCode));

            Log::info('Password reset email sent to: ' . $this->user->email);
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email: ' . $e->getMessage());

            // Повторная попытка через 5 минут, если не удалось
            if ($this->attempts() < 3) {
                $this->release(300);
            }
        }
    }
}
