<?php

namespace App\Policies;

use App\Enums\AppointmentStatus;
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
     * Determine whether the user can confirm the appointment.
     */
    public function confirm(User $user, Appointment $appointment): bool
    {
        if ($appointment->status !== AppointmentStatus::Pending && $appointment->status !== 'pending') {
            return false;
        }

        return $user->isAdmin() || $user->isReceptionist();
    }

    /**
     * Determine whether the user can mark the appointment in progress (en sala).
     */
    public function markInProgress(User $user, Appointment $appointment): bool
    {
        if ($appointment->status !== AppointmentStatus::Confirmed && $appointment->status !== 'confirmed') {
            return false;
        }

        if ($user->isAdmin() || $user->isReceptionist()) {
            return true;
        }

        if ($user->isDentist()) {
            return $user->dentist_id !== null && (int) $appointment->dentist_id === (int) $user->dentist_id;
        }

        return false;
    }

    /**
     * Determine whether the user can complete the appointment.
     */
    public function complete(User $user, Appointment $appointment): bool
    {
        $validStatuses = [AppointmentStatus::Confirmed, AppointmentStatus::InProgress, 'confirmed', 'in_progress'];
        if (! in_array($appointment->status, $validStatuses, false)) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDentist()) {
            return $user->dentist_id !== null && (int) $appointment->dentist_id === (int) $user->dentist_id;
        }

        return false;
    }

    /**
     * Determine whether the user can reschedule the appointment.
     */
    public function reschedule(User $user, Appointment $appointment): bool
    {
        $validStatuses = [AppointmentStatus::Pending, AppointmentStatus::Confirmed, 'pending', 'confirmed'];
        if (! in_array($appointment->status, $validStatuses, false)) {
            return false;
        }

        return $user->isAdmin() || $user->isReceptionist();
    }

    /**
     * Determine whether the user can cancel the appointment.
     */
    public function cancel(User $user, Appointment $appointment): bool
    {
        $validStatuses = [AppointmentStatus::Pending, AppointmentStatus::Confirmed, 'pending', 'confirmed'];
        if (! in_array($appointment->status, $validStatuses, false)) {
            return false;
        }

        return $user->isAdmin() || $user->isReceptionist();
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
