<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(?User $user): bool
    {
        return true; // catalogue public
    }

    public function view(?User $user, Product $product): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isGestionnaire();
    }

    public function update(User $user, Product $product): bool
    {
        // Couvre aussi la désactivation et la gestion du stock/variantes/images (docs/SPEC.md §2.5)
        return $user->isGestionnaire();
    }

    public function delete(User $user, Product $product): bool
    {
        // Suppression définitive réservée à l'Administrateur/Super Administrateur
        return $user->isAdmin();
    }
}
