<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePksDutyAssignmentRequest;
use App\Http\Requests\UpdatePksDutyAssignmentRequest;
use App\Models\PksDutyAssignment;
use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PksDutyAssignmentController extends Controller
{
    /**
     * Display a listing of PKS duty assignments.
     */
    public function index(Request $request): View
    {
        $query = PksDutyAssignment::query()
            ->with(['schedule.shift', 'member.student', 'location']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('member.student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('pks_duty_schedule_id')) {
            $query->where('pks_duty_schedule_id', $request->pks_duty_schedule_id);
        }

        if ($request->filled('pks_shift_id')) {
            $query->whereHas('schedule', function ($q) use ($request) {
                $q->where('pks_shift_id', $request->pks_shift_id);
            });
        }

        if ($request->filled('pks_duty_location_id')) {
            $query->where('pks_duty_location_id', $request->pks_duty_location_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereHas('schedule', function ($q) use ($request) {
                $q->whereDate('schedule_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('schedule', function ($q) use ($request) {
                $q->whereDate('schedule_date', '<=', $request->date_to);
            });
        }

        $assignments = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Get filter options
        $schoolId = auth()->user()->school_id;
        $schedules = PksDutySchedule::query()
            ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
            ->orderBy('schedule_date', 'desc')
            ->get();

        $locations = PksDutyLocation::active()
            ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
            ->orderBy('name')
            ->get();

        return view('pks-duty-assignments.index', compact('assignments', 'schedules', 'locations'));
    }

    /**
     * Show the form for creating a new PKS duty assignment.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', PksDutyAssignment::class);

        $schoolId = auth()->user()->school_id;

        // Get schedule if provided in query string
        $selectedSchedule = null;
        if ($request->filled('schedule_id')) {
            $selectedSchedule = PksDutySchedule::query()
                ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
                ->find($request->schedule_id);
        }

        // Get schedules (only scheduled ones that can receive assignments)
        $schedules = PksDutySchedule::query()
            ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
            ->where('status', PksDutySchedule::STATUS_SCHEDULED)
            ->with(['shift', 'locations'])
            ->orderBy('schedule_date', 'desc')
            ->get();

        // Get active PKS members
        $members = PksMember::active()
            ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
            ->with('student')
            ->orderBy('position')
            ->get();

        // Get locations (based on selected schedule)
        $locations = collect();
        if ($selectedSchedule) {
            $locations = $selectedSchedule->locations;
        }

        return view('pks-duty-assignments.create', compact(
            'schedules',
            'selectedSchedule',
            'members',
            'locations'
        ));
    }

    /**
     * Store a newly created PKS duty assignment.
     */
    public function store(StorePksDutyAssignmentRequest $request): RedirectResponse
    {
        $this->authorize('create', PksDutyAssignment::class);

        $validated = $request->validated();

        $assignment = DB::transaction(function () use ($validated) {
            return PksDutyAssignment::create([
                'school_id' => $this->getSchoolId(),
                'pks_duty_schedule_id' => $validated['pks_duty_schedule_id'],
                'pks_member_id' => $validated['pks_member_id'],
                'pks_duty_location_id' => $validated['pks_duty_location_id'],
                'status' => $validated['status'] ?? PksDutyAssignment::STATUS_ASSIGNED,
                'assigned_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('pks-duty-schedules.show', $validated['pks_duty_schedule_id'])
            ->with('success', 'Penugasan piket berhasil ditambahkan.');
    }

    /**
     * Display the specified PKS duty assignment.
     */
    public function show(PksDutyAssignment $pksDutyAssignment): View
    {
        $this->authorize('view', $pksDutyAssignment);

        $pksDutyAssignment->load(['schedule.shift', 'member.student', 'location', 'school']);

        return view('pks-duty-assignments.show', compact('pksDutyAssignment'));
    }

    /**
     * Show the form for editing the specified PKS duty assignment.
     */
    public function edit(PksDutyAssignment $pksDutyAssignment): View
    {
        $this->authorize('update', $pksDutyAssignment);

        $schoolId = auth()->user()->school_id;

        // Get schedules
        $schedules = PksDutySchedule::query()
            ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
            ->where('status', PksDutySchedule::STATUS_SCHEDULED)
            ->with(['shift', 'locations'])
            ->orderBy('schedule_date', 'desc')
            ->get();

        // Get active PKS members
        $members = PksMember::active()
            ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
            ->with('student')
            ->orderBy('position')
            ->get();

        // Load relationships
        $pksDutyAssignment->load(['schedule.shift', 'schedule.locations', 'member.student', 'location']);

        return view('pks-duty-assignments.edit', compact(
            'pksDutyAssignment',
            'schedules',
            'members'
        ));
    }

    /**
     * Update the specified PKS duty assignment.
     */
    public function update(UpdatePksDutyAssignmentRequest $request, PksDutyAssignment $pksDutyAssignment): RedirectResponse
    {
        $this->authorize('update', $pksDutyAssignment);

        $validated = $request->validated();

        $pksDutyAssignment->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('pks-duty-schedules.show', $pksDutyAssignment->pks_duty_schedule_id)
            ->with('success', 'Penugasan piket berhasil diperbarui.');
    }

    /**
     * Get school ID.
     */
    protected function getSchoolId(): int
    {
        return auth()->user()->school_id ?? 0;
    }
}
