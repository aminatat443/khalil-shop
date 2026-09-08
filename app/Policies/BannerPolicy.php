<?php

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;

class BannerPolicy
{
    public function viewAny(User $user): bool
    {
        // Gestion homepage réservée à l'Administrateur/Super Administrateur (docs/SPEC.md §2.5)
        return $user->isAdmin();
    }

    public function view(User $user, Banner $banner): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Banner $banner): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Banner $banner): bool
    {
        return $user->isAdmin();
    }
}
