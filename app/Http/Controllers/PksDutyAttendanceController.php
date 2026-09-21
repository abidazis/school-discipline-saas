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
        $query = PksDutyAttendance::query()
            ->with(['assignment.member.student', 'assignment.schedule.shift', 'assignment.location', 'recordedBy']);

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

        // Get filter options
        $schoolId = auth()->user()->school_id;

        $schedules = PksDutySchedule::query()
            ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
            ->orderBy('schedule_date', 'desc')
            ->get();

        return view('pks-duty-attendances.index', compact('attendances', 'schedules'));
    }

    /**
     * Show the form for creating a new PKS duty attendance.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', PksDutyAttendance::class);

        $schoolId = auth()->user()->school_id;

        // Get assignment if provided in query string
        $assignment = null;
        if ($request->filled('assignment_id')) {
            $assignment = PksDutyAssignment::with(['member.student', 'member.student.schoolClass', 'schedule.shift', 'location'])
                ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
                ->find($request->assignment_id);
        }

        return view('pks-duty-attendances.create', compact('assignment'));
    }

    /**
     * Store a newly created PKS duty attendance.
     */
    public function store(StorePksDutyAttendanceRequest $request): RedirectResponse
    {
        $this->authorize('create', PksDutyAttendance::class);

        $validated = $request->validated();

        $attendance = PksDutyAttendance::create([
            'school_id' => $this->getSchoolId(),
            'pks_duty_assignment_id' => $validated['pks_duty_assignment_id'],
            'status' => $validated['status'],
            'check_in_at' => $validated['check_in_at'] ?? null,
            'check_out_at' => $validated['check_out_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'recorded_by' => auth()->id(),
        ]);

        // Get the assignment to redirect back to schedule
        $assignment = PksDutyAssignment::find($validated['pks_duty_assignment_id']);

        return redirect()->route('pks-duty-schedules.show', $assignment->pks_duty_schedule_id)
            ->with('success', 'Kehadiran berhasil disimpan.');
    }

    /**
     * Display the specified PKS duty attendance.
     */
    public function show(PksDutyAttendance $pksDutyAttendance): View
    {
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
