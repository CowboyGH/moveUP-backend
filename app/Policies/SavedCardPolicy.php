<?php

namespace App\Policies;

use App\Models\SavedCard;
use App\Models\User;

class SavedCardPolicy
{
    public function delete(User $user, SavedCard $card): bool
    {
        return $user->id === $card->user_id;
    }

    public function update(User $user, SavedCard $card): bool
    {
        return $user->id === $card->user_id;
    }
}
