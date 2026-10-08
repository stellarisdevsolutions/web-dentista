<?php

namespace App\Policies;

use App\Models\Dentist;
use App\Models\User;

class DentistPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isReceptionist() || $user->isDentist();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Dentist $dentist): bool
    {
        return $user->isAdmin() || $user->isReceptionist() || $user->isDentist();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Dentist $dentist): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDentist()) {
            return $user->dentist_id !== null && $dentist->id === $user->dentist_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Dentist $dentist): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can bulk delete models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
