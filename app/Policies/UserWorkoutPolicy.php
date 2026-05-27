<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserWorkout;

class UserWorkoutPolicy
{
    public function view(User $user, UserWorkout $userWorkout): bool
    {
        return $user->id === $userWorkout->user_id;
    }

    public function update(User $user, UserWorkout $userWorkout): bool
    {
        return $user->id === $userWorkout->user_id;
    }

    public function delete(User $user, UserWorkout $userWorkout): bool
    {
        return $user->id === $userWorkout->user_id;
    }

    public function start(User $user, UserWorkout $userWorkout): bool
    {
        return $user->id === $userWorkout->user_id;
    }

    public function complete(User $user, UserWorkout $userWorkout): bool
    {
        return $user->id === $userWorkout->user_id;
    }
}
