<?php

namespace App\Providers;

use App\Models\PksDutyAssignment;
use App\Models\PksDutyAttendance;
use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksFieldActivity;
use App\Models\PksShift;
use App\Models\Violation;
use App\Models\ViolationEvidence;
use App\Models\ViolationType;
use App\Policies\PksDutyAssignmentPolicy;
use App\Policies\PksDutyAttendancePolicy;
use App\Policies\PksDutyLocationPolicy;
use App\Policies\PksDutySchedulePolicy;
use App\Policies\PksFieldActivityPolicy;
use App\Policies\PksMemberPolicy;
use App\Policies\PksShiftPolicy;
use App\Policies\ViolationEvidencePolicy;
use App\Policies\ViolationPolicy;
use App\Policies\ViolationTypePolicy;
use App\Policies\PksReportPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies explicitly for clarity
        Gate::policy(ViolationType::class, ViolationTypePolicy::class);
        Gate::policy(Violation::class, ViolationPolicy::class);
        Gate::policy(ViolationEvidence::class, ViolationEvidencePolicy::class);
        Gate::policy(\App\Models\PksMember::class, PksMemberPolicy::class);
        Gate::policy(PksShift::class, PksShiftPolicy::class);
        Gate::policy(PksDutyLocation::class, PksDutyLocationPolicy::class);
        Gate::policy(PksDutySchedule::class, PksDutySchedulePolicy::class);
        Gate::policy(PksDutyAssignment::class, PksDutyAssignmentPolicy::class);
        Gate::policy(PksDutyAttendance::class, PksDutyAttendancePolicy::class);
        Gate::policy(PksFieldActivity::class, PksFieldActivityPolicy::class);

        // Register report authorization gates
        Gate::define('viewDailyPksReport', [PksReportPolicy::class, 'viewDaily']);
        Gate::define('exportDailyPdf', [PksReportPolicy::class, 'exportDailyPdf']);
        Gate::define('viewMonthlyPksReport', [PksReportPolicy::class, 'viewMonthly']);
        Gate::define('exportMonthlyExcel', [PksReportPolicy::class, 'exportMonthlyExcel']);
        Gate::define('accessSchoolForReport', [PksReportPolicy::class, 'accessSchool']);

        // Gates for PKS Team access
        Gate::define('pks-team-access', function ($user) {
            return $user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isPksTeam();
        });

        Gate::define('view-violations', function ($user) {
            return $user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isPksTeam() || $user->isOperator() || $user->isTeacher();
        });

        Gate::define('create-violations', function ($user) {
            return $user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isPksTeam() || $user->isOperator() || $user->isTeacher();
        });

        // PKS Operational - hanya Super Admin, School Admin, Operator
        Gate::define('view-pks-operational', function ($user) {
            return $user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isOperator();
        });

        // PKS Team can view schedules and assignments (read-only access)
        Gate::define('pks-schedule-view', function ($user) {
            return $user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isOperator() || $user->isPksTeam();
        });

        Gate::define('manage-master-data', function ($user) {
            return $user->isSuperAdmin() || $user->isSchoolAdmin();
        });
    }
}
