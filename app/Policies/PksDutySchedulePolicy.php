<?php

namespace App\Policies;

use App\Models\PksDutySchedule;
use App\Models\User;

class PksDutySchedulePolicy
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
    public function view(User $user, PksDutySchedule $pksDutySchedule): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $pksDutySchedule->school_id === $user->school_id;
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
    public function update(User $user, PksDutySchedule $pksDutySchedule): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isSchoolAdmin()) {
            return false;
        }

        return $pksDutySchedule->school_id === $user->school_id;
    }
}
