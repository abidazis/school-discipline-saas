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

        // Check school access
        if ($pksDutyAttendance->school_id !== $user->school_id) {
            return false;
        }

        // For PKS Team, allow viewing their own attendance only
        if ($user->isPksTeam()) {
            // Load the relationship if not already loaded
            if (!$pksDutyAttendance->relationLoaded('assignment')) {
                $pksDutyAttendance->load('assignment.member');
            }

            $assignmentMember = $pksDutyAttendance->assignment?->member;
            if (!$assignmentMember) {
                return false;
            }

            // Check if member is linked to user
            if (!$assignmentMember->user_id) {
                return false;
            }

            return $assignmentMember->user_id === $user->id;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isPembinaPks() || $user->isPksTeam();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PksDutyAttendance $pksDutyAttendance): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isSchoolAdmin() && !$user->isPembinaPks()) {
            return false;
        }

        return $pksDutyAttendance->school_id === $user->school_id;
    }
}
