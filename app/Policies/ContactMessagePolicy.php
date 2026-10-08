<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;

class ContactMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isReceptionist();
    }

    public function view(User $user, ContactMessage $contactMessage): bool
    {
        return $user->isAdmin() || $user->isReceptionist();
    }

    public function create(User $user): bool
    {
        return false; // Created from public contact form
    }

    public function update(User $user, ContactMessage $contactMessage): bool
    {
        return $user->isAdmin() || $user->isReceptionist();
    }

    public function delete(User $user, ContactMessage $contactMessage): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
