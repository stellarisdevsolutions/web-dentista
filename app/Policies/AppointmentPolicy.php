<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
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
    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin() || $user->isReceptionist()) {
            return true;
        }

        if ($user->isDentist()) {
            return $user->dentist_id !== null && $appointment->dentist_id === $user->dentist_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isReceptionist();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin() || $user->isReceptionist()) {
            return true;
        }

        if ($user->isDentist()) {
            return $user->dentist_id !== null && $appointment->dentist_id === $user->dentist_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appointment $appointment): bool
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
