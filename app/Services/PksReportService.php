<?php

namespace App\Services;

use App\Models\PksDutyAssignment;
use App\Models\PksDutyAttendance;
use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksFieldActivity;
use App\Models\PksShift;
use App\Models\Student;
use App\Models\Violation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PksReportService
{
    /**
     * Get daily report data.
     */
    public function getDailyReport(
        ?int $schoolId,
        Carbon $date,
        ?int $shiftId = null,
        ?int $locationId = null,
        ?string $status = null
    ): array {
        // Get schedules for the date
        $schedulesQuery = PksDutySchedule::query()
            ->with([
                'shift',
                'locations',
                'assignments' => function ($query) {
                    $query->with([
                        'member.student.schoolClass',
                        'location',
                        'attendance',
                        'fieldActivities' => function ($query) {
                            $query->completed();
                        },
                    ]);
                },
            ])
            ->where('school_id', $schoolId)
            ->whereDate('schedule_date', $date->toDateString());

        if ($shiftId) {
            $schedulesQuery->where('pks_shift_id', $shiftId);
        }

        if ($locationId) {
            $schedulesQuery->whereHas('locations', function ($query) use ($locationId) {
                $query->where('pks_duty_locations.id', $locationId);
            });
        }

        if ($status) {
            $schedulesQuery->where('status', $status);
        }

        $schedules = $schedulesQuery->get();

        // Get field activities for the date
        $activities = PksFieldActivity::with([
            'assignment.member.student',
            'assignment.location',
            'assignment.schedule.shift',
            'violation',
        ])
            ->where('school_id', $schoolId)
            ->whereDate('activity_date', $date->toDateString())
            ->when($shiftId, function ($query) use ($shiftId) {
                $query->whereHas('assignment.schedule', function ($q) use ($shiftId) {
                    $q->where('pks_shift_id', $shiftId);
                });
            })
            ->when($locationId, function ($query) use ($locationId) {
                $query->whereHas('assignment', function ($q) use ($locationId) {
                    $q->where('pks_duty_location_id', $locationId);
                });
            })
            ->completed()
            ->get();

        // Get violations for the date
        $violations = Violation::with([
            'student',
            'violationType',
            'schoolClass',
        ])
            ->where('school_id', $schoolId)
            ->whereDate('occurred_at', $date->toDateString())
            ->active()
            ->get();

        // Calculate summaries
        $summary = $this->calculateDailySummary($schedules, $activities, $violations);

        return [
            'date' => $date,
            'schedules' => $schedules,
            'activities' => $activities,
            'violations' => $violations,
            'summary' => $summary,
            'filters' => [
                'shift_id' => $shiftId,
                'location_id' => $locationId,
                'status' => $status,
            ],
        ];
    }

    /**
     * Get monthly report data.
     */
    public function getMonthlyReport(
        ?int $schoolId,
        int $month,
        int $year,
        ?int $shiftId = null,
        ?int $locationId = null
    ): array {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get schedules for the month
        $schedulesQuery = PksDutySchedule::query()
            ->with([
                'shift',
                'locations',
                'assignments' => function ($query) {
                    $query->with([
                        'member.student',
                        'location',
                        'attendance',
                    ]);
                },
            ])
            ->where('school_id', $schoolId)
            ->whereBetween('schedule_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($shiftId) {
            $schedulesQuery->where('pks_shift_id', $shiftId);
        }

        if ($locationId) {
            $schedulesQuery->whereHas('locations', function ($query) use ($locationId) {
                $query->where('pks_duty_locations.id', $locationId);
            });
        }

        $schedules = $schedulesQuery->get();

        // Get all assignments from these schedules
        $allAssignments = $schedules->pluck('assignments')->flatten();

        // Get field activities for the month
        $activities = PksFieldActivity::with([
            'assignment.member.student',
            'assignment.location',
            'assignment.schedule.shift',
        ])
            ->where('school_id', $schoolId)
            ->whereBetween('activity_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($shiftId, function ($query) use ($shiftId) {
                $query->whereHas('assignment.schedule', function ($q) use ($shiftId) {
                    $q->where('pks_shift_id', $shiftId);
                });
            })
            ->completed()
            ->get();

        // Get violations for the month
        $violations = Violation::with([
            'student',
            'violationType',
        ])
            ->where('school_id', $schoolId)
            ->whereBetween('occurred_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->active()
            ->get();

        // Calculate summaries
        $summary = $this->calculateMonthlySummary($schedules, $allAssignments, $activities, $violations);

        return [
            'month' => $month,
            'year' => $year,
            'period' => $startDate->format('F Y'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'schedules' => $schedules,
            'activities' => $activities,
            'violations' => $violations,
            'summary' => $summary,
            'filters' => [
                'shift_id' => $shiftId,
                'location_id' => $locationId,
            ],
        ];
    }

    /**
     * Calculate daily summary.
     */
    protected function calculateDailySummary(Collection $schedules, Collection $activities, Collection $violations): array
    {
        $totalSchedules = $schedules->count();
        $allAssignments = $schedules->pluck('assignments')->flatten();

        $totalAssignments = $allAssignments->count();

        $attendances = $allAssignments->pluck('attendance')->filter();

        $presentCount = $attendances->where('status', PksDutyAttendance::STATUS_PRESENT)->count();
        $lateCount = $attendances->where('status', PksDutyAttendance::STATUS_LATE)->count();
        $absentCount = $attendances->where('status', PksDutyAttendance::STATUS_ABSENT)->count();
        $excusedCount = $attendances->where('status', PksDutyAttendance::STATUS_EXCUSED)->count();
        $notRecordedCount = $totalAssignments - $attendances->count();

        return [
            'total_schedules' => $totalSchedules,
            'total_assignments' => $totalAssignments,
            'total_attendances' => $attendances->count(),
            'present_count' => $presentCount,
            'late_count' => $lateCount,
            'absent_count' => $absentCount,
            'excused_count' => $excusedCount,
            'not_recorded_count' => $notRecordedCount,
            'total_activities' => $activities->count(),
            'total_violations' => $violations->count(),
        ];
    }

    /**
     * Calculate monthly summary.
     */
    protected function calculateMonthlySummary(
        Collection $schedules,
        Collection $assignments,
        Collection $activities,
        Collection $violations
    ): array {
        $totalSchedules = $schedules->count();
        $totalAssignments = $assignments->count();

        $attendances = $assignments->pluck('attendance')->filter();

        $presentCount = $attendances->where('status', PksDutyAttendance::STATUS_PRESENT)->count();
        $lateCount = $attendances->where('status', PksDutyAttendance::STATUS_LATE)->count();
        $absentCount = $attendances->where('status', PksDutyAttendance::STATUS_ABSENT)->count();
        $excusedCount = $attendances->where('status', PksDutyAttendance::STATUS_EXCUSED)->count();
        $notRecordedCount = $totalAssignments - $attendances->count();

        $totalPoints = $violations->sum('points');

        return [
            'total_schedules' => $totalSchedules,
            'total_assignments' => $totalAssignments,
            'total_attendances' => $attendances->count(),
            'present_count' => $presentCount,
            'late_count' => $lateCount,
            'absent_count' => $absentCount,
            'excused_count' => $excusedCount,
            'not_recorded_count' => $notRecordedCount,
            'total_activities' => $activities->count(),
            'total_violations' => $violations->count(),
            'total_points' => $totalPoints,
        ];
    }

    /**
     * Get shifts for filter.
     */
    public function getShifts(?int $schoolId): Collection
    {
        return PksShift::query()
            ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Get locations for filter.
     */
    public function getLocations(?int $schoolId): Collection
    {
        return PksDutyLocation::query()
            ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
            ->active()
            ->orderBy('name')
            ->get();
    }
}
