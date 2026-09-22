<?php

namespace App\Policies;

use App\Models\User;
use App\Models\School;
use Illuminate\Auth\Access\Response;

class PksReportPolicy
{
    /**
     * Anyone authenticated can view daily reports.
     */
    public function viewDaily(User $user): Response
    {
        return $this->canAccessReport($user);
    }

    /**
     * Anyone authenticated can export daily PDF.
     */
    public function exportDailyPdf(User $user): Response
    {
        return $this->canAccessReport($user);
    }

    /**
     * Anyone authenticated can view monthly reports.
     */
    public function viewMonthly(User $user): Response
    {
        return $this->canAccessReport($user);
    }

    /**
     * Anyone authenticated can export monthly Excel.
     */
    public function exportMonthlyExcel(User $user): Response
    {
        return $this->canAccessReport($user);
    }

    /**
     * Check if user can access reports.
     */
    protected function canAccessReport(User $user): Response
    {
        // User must be authenticated
        if (!$user) {
            return Response::deny('User must be authenticated.');
        }

        // User must have a school (unless super admin)
        if (!$user->isSuperAdmin() && !$user->school_id) {
            return Response::deny('User must belong to a school.');
        }

        return Response::allow();
    }

    /**
     * Check if user can access a specific school for reporting.
     */
    public function accessSchool(User $user, ?int $schoolId): Response
    {
        if (!$schoolId) {
            return Response::allow();
        }

        if ($user->canManageSchool($schoolId)) {
            return Response::allow();
        }

        return Response::deny('You do not have access to this school.');
    }
}
