<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Violation;

class ViolationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view violations
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Violation $violation): bool
    {
        // Super admin can view all
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Other users can only view violations from their school
        return $violation->school_id === $user->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Super admin, school admin, operator, and teacher can create violations
        return $user->isSuperAdmin() ||
               $user->isSchoolAdmin() ||
               $user->isOperator() ||
               $user->isTeacher();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Violation $violation): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isSchoolAdmin()) {
            return false;
        }

        // Can only update violations from own school
        if ($violation->school_id !== $user->school_id) {
            return false;
        }

        // Can only update recorded violations
        return $violation->canEdit();
    }

    /**
     * Determine whether the user can verify the model.
     */
    public function verify(User $user, Violation $violation): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isSchoolAdmin()) {
            return false;
        }

        // Can only verify violations from own school
        return $violation->school_id === $user->school_id;
    }

    /**
     * Determine whether the user can cancel the model.
     */
    public function cancel(User $user, Violation $violation): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isSchoolAdmin()) {
            return false;
        }

        // Can only cancel violations from own school
        return $violation->school_id === $user->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Violation $violation): bool
    {
        // Violations should not be deleted, only cancelled
        return false;
    }
}
