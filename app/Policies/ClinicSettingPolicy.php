<?php

namespace App\Policies;

use App\Models\ClinicSetting;
use App\Models\User;

class ClinicSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ClinicSetting $clinicSetting): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return false; // Singleton table
    }

    public function update(User $user, ClinicSetting $clinicSetting): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ClinicSetting $clinicSetting): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
