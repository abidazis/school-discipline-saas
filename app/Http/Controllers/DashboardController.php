<?php

namespace App\Http\Controllers;

use App\Models\PksDutySchedule;
use App\Models\PksMember;
use App\Models\Student;
use App\Models\Violation;
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

        return view('dashboard', compact('stats'));
    }
}
