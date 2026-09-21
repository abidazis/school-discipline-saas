<?php

namespace App\Policies;

use App\Models\PksDutyAttendance;
use App\Models\User;

class PksDutyAttendancePolicy
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
    public function view(User $user, PksDutyAttendance $pksDutyAttendance): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $pksDutyAttendance->school_id === $user->school_id;
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
    public function update(User $user, PksDutyAttendance $pksDutyAttendance): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isSchoolAdmin() && !$user->isOperator()) {
            return false;
        }

        return $pksDutyAttendance->school_id === $user->school_id;
    }
}
