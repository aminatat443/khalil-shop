<?php

namespace App\Policies;

use App\Models\User;

class SecurityEventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
