<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;

class CouponPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isGestionnaire();
    }

    public function view(User $user, Coupon $coupon): bool
    {
        return $user->isGestionnaire();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Coupon $coupon): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->isAdmin();
    }
}
