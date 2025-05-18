<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Auth\Access\Response;

class UserActivityPolicy
{
    /**
     * Determine whether the user can export activities.
     */
    public function export(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can clear activities.
     */
    public function clear(User $user): bool
    {
        return $user->isAdmin();
    }
}