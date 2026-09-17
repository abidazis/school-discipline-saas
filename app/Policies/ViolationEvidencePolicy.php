<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ViolationEvidence;

class ViolationEvidencePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ViolationEvidence $evidence): bool
    {
        // Super admin can view all
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Other users can only view evidence from their school
        return $evidence->violation->school_id === $user->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Super admin, school admin, operator, and teacher can add evidence
        return $user->isSuperAdmin() ||
               $user->isSchoolAdmin() ||
               $user->isOperator() ||
               $user->isTeacher();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ViolationEvidence $evidence): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isSchoolAdmin() && !$user->isOperator()) {
            return false;
        }

        // Can only delete evidence from own school
        return $evidence->violation->school_id === $user->school_id;
    }
}
