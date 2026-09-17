<?php

namespace App\Policies;

use App\Models\PksMember;
use App\Models\User;

class PksMemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view PKS members
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PksMember $pksMember): bool
    {
        // Super admin can view all
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Other users can only view PKS members from their school
        return $pksMember->school_id === $user->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isSchoolAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PksMember $pksMember): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isSchoolAdmin()) {
            return false;
        }

        return $pksMember->school_id === $user->school_id;
    }
}
