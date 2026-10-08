<?php

namespace App\Policies;

use App\Models\SmileCase;
use App\Models\User;

class SmileCasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, SmileCase $smileCase): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, SmileCase $smileCase): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, SmileCase $smileCase): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
