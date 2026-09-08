<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        // File de modération réservée au back-office (section 39 du cahier des charges)
        return $user->isGestionnaire();
    }

    public function update(User $user, Review $review): bool
    {
        return $user->isGestionnaire();
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->isGestionnaire();
    }
}
