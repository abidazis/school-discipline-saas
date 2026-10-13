<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\PksDutyAssignment;
use App\Models\PksDutyAttendance;
use App\Models\PksDutySchedule;
use App\Models\PksMember;
use App\Models\Student;
use App\Models\Violation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        // Build stats based on user role
        if ($user->isSuperAdmin()) {
            // Super admin sees global stats
            $stats = [
                'totalStudents' => Student::count(),
                'activeStudents' => Student::where('status', 'active')->count(),
                'totalViolations' => Violation::count(),
                'totalPksMembers' => PksMember::where('status', 'active')->count(),
                'totalSchedules' => PksDutySchedule::count(),
            ];
        } else {
            // School-specific stats
            $stats = [
                'totalStudents' => Student::where('school_id', $schoolId)->count(),
                'activeStudents' => Student::where('school_id', $schoolId)->where('status', 'active')->count(),
                'totalViolations' => Violation::where('school_id', $schoolId)->count(),
                'totalPksMembers' => PksMember::where('school_id', $schoolId)->where('status', 'active')->count(),
                'totalSchedules' => PksDutySchedule::where('school_id', $schoolId)->count(),
            ];
        }

        // PKS Team specific data
        $pksData = null;
        if ($user->isPksTeam()) {
            $pksData = $this->getPksTeamData($user, $schoolId);
        }

        return view('dashboard', compact('stats', 'pksData'));
    }

    /**
     * Get PKS Team specific data for dashboard.
     */
    protected function getPksTeamData($user, $schoolId): array
    {
        $today = Carbon::today();

        // Get PKS member profile
        $pksMember = PksMember::where('user_id', $user->id)
            ->where('status', PksMember::STATUS_ACTIVE)
            ->first();

        // Get today's assignments
        $todayAssignments = collect();
        if ($pksMember) {
            $todayAssignments = PksDutyAssignment::where('pks_member_id', $pksMember->id)
                ->whereHas('schedule', function ($q) use ($today, $schoolId) {
                    $q->where('schedule_date', $today)
                      ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId));
                })
                ->with(['schedule.shift', 'location', 'attendance'])
                ->get();
        }

        // Get today's attendance status
        $attendanceToday = null;
        if ($todayAssignments->isNotEmpty()) {
            $assignment = $todayAssignments->first();
            $attendanceToday = PksDutyAttendance::where('pks_duty_assignment_id', $assignment->id)->first();
        }

        // Get recent violations recorded by this user
        $recentViolations = Violation::where('officer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->with(['student', 'violationType'])
            ->get();

        // Get violation stats for this user
        $violationStats = [
            'today' => Violation::where('officer_id', $user->id)
                ->whereDate('created_at', $today)
                ->count(),
            'week' => Violation::where('officer_id', $user->id)
                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
            'total' => Violation::where('officer_id', $user->id)->count(),
        ];

        return [
            'pksMember' => $pksMember,
            'todayAssignments' => $todayAssignments,
            'attendanceToday' => $attendanceToday,
            'recentViolations' => $recentViolations,
            'violationStats' => $violationStats,
        ];
    }
}
