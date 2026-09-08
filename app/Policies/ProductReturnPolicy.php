<?php

namespace App\Policies;

use App\Models\ProductReturn;
use App\Models\User;

class ProductReturnPolicy
{
    /**
     * File des demandes de retour — réservée au back-office (docs/SPEC.md §2.1).
     */
    public function viewAny(User $user): bool
    {
        return $user->isGestionnaire();
    }

    public function update(User $user, ProductReturn $return): bool
    {
        return $user->isGestionnaire();
    }
}
