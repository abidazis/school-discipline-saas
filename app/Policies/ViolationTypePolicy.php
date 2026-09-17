<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ViolationType;

class ViolationTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view violation types
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ViolationType $violationType): bool
    {
        // Super admin can view all
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Other users can only view violation types from their school
        return $violationType->school_id === $user->school_id;
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
    public function update(User $user, ViolationType $violationType): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isSchoolAdmin()) {
            return false;
        }

        return $violationType->school_id === $user->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ViolationType $violationType): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isSchoolAdmin()) {
            return false;
        }

        // Cannot delete violation type that is already used
        if ($violationType->isUsed()) {
            return false;
        }

        return $violationType->school_id === $user->school_id;
    }
}
