<?php

namespace App\Policies;

use App\Models\TestAttempt;
use App\Models\User;

class TestAttemptPolicy
{
    public function view(User $user, TestAttempt $attempt): bool
    {
        return $user->id === $attempt->user_id;
    }

    public function update(User $user, TestAttempt $attempt): bool
    {
        return $user->id === $attempt->user_id;
    }

    public function complete(User $user, TestAttempt $attempt): bool
    {
        return $user->id === $attempt->user_id;
    }
}
