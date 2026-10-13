<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePksDutyAttendanceRequest;
use App\Http\Requests\UpdatePksDutyAttendanceRequest;
use App\Models\PksDutyAssignment;
use App\Models\PksDutyAttendance;
use App\Models\PksDutySchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PksDutyAttendanceController extends Controller
{
    /**
     * Display a listing of PKS duty attendances.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        $query = PksDutyAttendance::query()
            ->with(['assignment.member.student', 'assignment.schedule.shift', 'assignment.location', 'recordedBy']);

        // Apply tenant scope
        $query->where('school_id', $schoolId);

        // For PKS Team, only show their own attendances
        if ($user->isPksTeam()) {
            $pksMember = $user->pksMember;
            if ($pksMember) {
                $query->whereHas('assignment', function ($q) use ($pksMember) {
                    $q->where('pks_member_id', $pksMember->id);
                });
            } else {
                // PKS Team without member record - return empty
                $query->whereRaw('1 = 0');
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('assignment.member.student', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('pks_duty_schedule_id')) {
            $query->whereHas('assignment', function ($q) use ($request) {
                $q->where('pks_duty_schedule_id', $request->pks_duty_schedule_id);
            });
        }

        if ($request->filled('pks_shift_id')) {
            $query->whereHas('assignment.schedule', function ($q) use ($request) {
                $q->where('pks_shift_id', $request->pks_shift_id);
            });
        }

        if ($request->filled('pks_duty_location_id')) {
            $query->whereHas('assignment', function ($q) use ($request) {
                $q->where('pks_duty_location_id', $request->pks_duty_location_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereHas('assignment.schedule', function ($q) use ($request) {
                $q->whereDate('schedule_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('assignment.schedule', function ($q) use ($request) {
                $q->whereDate('schedule_date', '<=', $request->date_to);
            });
        }

        $attendances = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Get filter options - only schedules that have assignments for this member (for PKS Team)
        $schedulesQuery = PksDutySchedule::query()->where('school_id', $schoolId);

        if ($user->isPksTeam()) {
            $pksMember = $user->pksMember;
            if ($pksMember) {
                $schedulesQuery->whereHas('assignments', function ($q) use ($pksMember) {
                    $q->where('pks_member_id', $pksMember->id);
                });
            }
        }

        $schedules = $schedulesQuery->orderBy('schedule_date', 'desc')->get();

        return view('pks-duty-attendances.index', compact('attendances', 'schedules'));
    }

    /**
     * Show the form for creating a new PKS duty attendance.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', PksDutyAttendance::class);

        $user = auth()->user();
        $schoolId = $user->school_id;

        // Get assignment if provided in query string
        $assignment = null;
        if ($request->filled('assignment_id')) {
            $assignmentQuery = PksDutyAssignment::with(['member.student', 'member.student.schoolClass', 'schedule.shift', 'location'])
                ->where('school_id', $schoolId);

            // For PKS Team, verify assignment belongs to them
            if ($user->isPksTeam()) {
                $pksMember = $user->pksMember;
                if ($pksMember) {
                    $assignmentQuery->where('pks_member_id', $pksMember->id);
                }
            }

            $assignment = $assignmentQuery->find($request->assignment_id);

            // Check if attendance already exists
            if ($assignment && $assignment->attendance) {
                return redirect()->route('pks-duty-attendances.show', $assignment->attendance)
                    ->with('info', 'Kehadiran untuk penugasan ini sudah tercatat.');
            }
        }

        // Get eligible assignments for dropdown (for non-PKS or when no assignment selected)
        $eligibleAssignments = null;
        if (!$assignment && $user->isPksTeam()) {
            $pksMember = $user->pksMember;
            if ($pksMember) {
                $eligibleAssignments = PksDutyAssignment::with(['member.student', 'schedule.shift', 'location', 'attendance'])
                    ->where('school_id', $schoolId)
                    ->where('pks_member_id', $pksMember->id)
                    ->where('status', PksDutyAssignment::STATUS_ASSIGNED)
                    ->whereDoesntHave('attendance')
                    ->whereHas('schedule', function ($q) {
                        $q->whereIn('status', [PksDutySchedule::STATUS_SCHEDULED, PksDutySchedule::STATUS_COMPLETED]);
                    })
                    ->orderBy('schedule.schedule_date', 'desc')
                    ->get();
            }
        }

        return view('pks-duty-attendances.create', compact('assignment', 'eligibleAssignments'));
    }

    /**
     * Store a newly created PKS duty attendance.
     */
    public function store(StorePksDutyAttendanceRequest $request): RedirectResponse
    {
        $this->authorize('create', PksDutyAttendance::class);

        $user = auth()->user();
        $validated = $request->validated();

        // Additional validation for PKS Team - verify assignment belongs to them
        if ($user->isPksTeam()) {
            $pksMember = $user->pksMember;
            if (!$pksMember) {
                return redirect()->back()->with('error', 'Profil PKS tidak ditemukan.');
            }

            $assignment = PksDutyAssignment::find($validated['pks_duty_assignment_id']);
            if (!$assignment || $assignment->pks_member_id !== $pksMember->id) {
                return redirect()->back()->with('error', 'Penugasan tidak valid.');
            }

            // Check if attendance already exists
            if ($assignment->attendance) {
                return redirect()->route('pks-duty-attendances.show', $assignment->attendance)
                    ->with('info', 'Kehadiran untuk penugasan ini sudah tercatat.');
            }
        }

        // Prepare attendance data
        $attendanceData = [
            'school_id' => $user->school_id,
            'pks_duty_assignment_id' => $validated['pks_duty_assignment_id'],
            'status' => $validated['status'],
            'check_in_at' => $validated['check_in_at'] ?? null,
            'check_out_at' => $validated['check_out_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'recorded_by' => auth()->id(),
        ];

        // Handle photo upload for PKS Team
        if ($user->isPksTeam() && $request->hasFile('photo')) {
            $photo = $request->file('photo');
            $hash = hash_file('sha256', $photo->getRealPath());

            // Store photo
            $path = $photo->store('attendances/' . $user->school_id, 'public');

            $attendanceData['photo_path'] = $path;
            $attendanceData['photo_hash'] = $hash;
            $attendanceData['photo_captured_at'] = now();
        }

        $attendance = PksDutyAttendance::create($attendanceData);

        // Get the assignment to redirect back to schedule
        $assignment = PksDutyAssignment::find($validated['pks_duty_assignment_id']);

        // For PKS Team, redirect to attendances list instead of schedule
        if ($user->isPksTeam()) {
            return redirect()->route('pks-duty-attendances.index')
                ->with('success', 'Kehadiran berhasil disimpan.');
        }

        return redirect()->route('pks-duty-schedules.show', $assignment->pks_duty_schedule_id)
            ->with('success', 'Kehadiran berhasil disimpan.');
    }

    /**
     * Display the specified PKS duty attendance.
     */
    public function show(PksDutyAttendance $pksDutyAttendance): View
    {
        // Load relationships before authorization check (needed for PKS Team policy)
        $pksDutyAttendance->load([
            'assignment.member',
        ]);

        $this->authorize('view', $pksDutyAttendance);

        $pksDutyAttendance->load([
            'assignment.member.student',
            'assignment.schedule.shift',
            'assignment.location',
            'recordedBy',
            'school',
        ]);

        return view('pks-duty-attendances.show', compact('pksDutyAttendance'));
    }

    /**
     * Show the form for editing the specified PKS duty attendance.
     */
    public function edit(PksDutyAttendance $pksDutyAttendance): View
    {
        $this->authorize('update', $pksDutyAttendance);

        $pksDutyAttendance->load([
            'assignment.member.student',
            'assignment.schedule.shift',
            'assignment.location',
        ]);

        return view('pks-duty-attendances.edit', compact('pksDutyAttendance'));
    }

    /**
     * Update the specified PKS duty attendance.
     */
    public function update(UpdatePksDutyAttendanceRequest $request, PksDutyAttendance $pksDutyAttendance): RedirectResponse
    {
        $this->authorize('update', $pksDutyAttendance);

        $validated = $request->validated();

        $pksDutyAttendance->update([
            'status' => $validated['status'],
            'check_in_at' => $validated['check_in_at'] ?? null,
            'check_out_at' => $validated['check_out_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('pks-duty-schedules.show', $pksDutyAttendance->assignment->pks_duty_schedule_id)
            ->with('success', 'Kehadiran berhasil diperbarui.');
    }

    /**
     * Get school ID.
     */
    protected function getSchoolId(): int
    {
        return auth()->user()->school_id ?? 0;
    }
}
