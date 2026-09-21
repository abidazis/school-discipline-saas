<?php

namespace App\Policies;

use App\Models\PksFieldActivity;
use App\Models\User;

class PksFieldActivityPolicy
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
    public function view(User $user, PksFieldActivity $pksFieldActivity): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $pksFieldActivity->school_id === $user->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isOperator();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PksFieldActivity $pksFieldActivity): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isSchoolAdmin() && !$user->isOperator()) {
            return false;
        }

        return $pksFieldActivity->school_id === $user->school_id;
    }

    /**
     * Determine whether the user can cancel the model.
     */
    public function cancel(User $user, PksFieldActivity $pksFieldActivity): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isSchoolAdmin() && !$user->isOperator()) {
            return false;
        }

        return $pksFieldActivity->school_id === $user->school_id;
    }
}
