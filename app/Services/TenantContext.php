<?php

namespace App\Services;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TenantContext
{
    protected ?School $currentSchool = null;

    /**
     * Set the current school context.
     */
    public function setCurrentSchool(?School $school): void
    {
        $this->currentSchool = $school;
    }

    /**
     * Get the current school context.
     */
    public function getCurrentSchool(): ?School
    {
        return $this->currentSchool;
    }

    /**
     * Get the current school ID.
     */
    public function getCurrentSchoolId(): ?int
    {
        return $this->currentSchool?->id;
    }

    /**
     * Resolve the current tenant from the authenticated user.
     */
    public function resolveFromUser(?User $user): void
    {
        if (!$user) {
            $this->currentSchool = null;
            return;
        }

        // Super admins have access to all schools
        if ($user->isSuperAdmin()) {
            // If user has a school_id, use it as default
            // Otherwise, they'll need to select a school
            $this->currentSchool = $user->school;
            return;
        }

        $this->currentSchool = $user->school;
    }

    /**
     * Check if a tenant context is set.
     */
    public function hasTenant(): bool
    {
        return $this->currentSchool !== null;
    }

    /**
     * Check if the current user can access the given school.
     */
    public function canAccessSchool(?int $schoolId): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->canManageSchool($schoolId);
    }
}
