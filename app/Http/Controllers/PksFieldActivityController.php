<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePksFieldActivityRequest;
use App\Http\Requests\UpdatePksFieldActivityRequest;
use App\Models\PksDutyAssignment;
use App\Models\PksDutySchedule;
use App\Models\PksFieldActivity;
use App\Models\Violation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PksFieldActivityController extends Controller
{
    /**
     * Display a listing of PKS field activities.
     */
    public function index(Request $request): View
    {
        $query = PksFieldActivity::query()
            ->with([
                'assignment.member.student',
                'assignment.schedule.shift',
                'assignment.location',
                'recordedBy',
                'violation.student',
            ]);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('finding', 'like', "%{$search}%")
                  ->orWhere('action_taken', 'like', "%{$search}%")
                  ->orWhereHas('assignment.member.student', function ($sq) use ($search) {
                      $sq->where('full_name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                  })
                  ->orWhereHas('assignment.location', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
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

        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('activity_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('activity_date', '<=', $request->date_to);
        }

        if ($request->filled('recorded_by')) {
            $query->where('recorded_by', $request->recorded_by);
        }

        $activities = $query->orderBy('activity_date', 'desc')
            ->orderBy('started_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Get filter options
        $schoolId = auth()->user()->school_id;

        $schedules = PksDutySchedule::query()
            ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
            ->orderBy('schedule_date', 'desc')
            ->get();

        return view('pks-field-activities.index', compact('activities', 'schedules'));
    }

    /**
     * Show the form for creating a new PKS field activity.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', PksFieldActivity::class);

        $schoolId = auth()->user()->school_id;

        // Get assignment if provided in query string
        $assignment = null;
        if ($request->filled('assignment_id')) {
            $assignment = PksDutyAssignment::with([
                'member.student',
                'member.student.schoolClass',
                'schedule.shift',
                'location',
            ])
                ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
                ->find($request->assignment_id);
        }

        // Get eligible assignments for dropdown
        $eligibleAssignments = collect();
        if (!$assignment) {
            $eligibleAssignments = PksDutyAssignment::query()
                ->where('school_id', $schoolId)
                ->where('status', 'assigned')
                ->whereHas('schedule', function ($q) {
                    $q->where('status', '!=', 'cancelled');
                })
                ->with(['member.student', 'schedule.shift', 'location'])
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
        }

        // Get active violations for linking
        $violations = Violation::query()
            ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
            ->whereIn('status', ['recorded', 'verified'])
            ->with('student')
            ->orderBy('occurred_at', 'desc')
            ->limit(50)
            ->get();

        $activityTypes = PksFieldActivity::TYPE_LABELS;

        return view('pks-field-activities.create', compact(
            'assignment',
            'eligibleAssignments',
            'violations',
            'activityTypes'
        ));
    }

    /**
     * Store a newly created PKS field activity.
     */
    public function store(StorePksFieldActivityRequest $request): RedirectResponse
    {
        $this->authorize('create', PksFieldActivity::class);

        $validated = $request->validated();

        $activity = PksFieldActivity::create([
            'school_id' => $this->getSchoolId(),
            'pks_duty_assignment_id' => $validated['pks_duty_assignment_id'],
            'activity_date' => $validated['activity_date'],
            'started_at' => $validated['started_at'],
            'ended_at' => $validated['ended_at'] ?? null,
            'activity_type' => $validated['activity_type'],
            'description' => $validated['description'] ?? null,
            'finding' => $validated['finding'] ?? null,
            'action_taken' => $validated['action_taken'] ?? null,
            'status' => $validated['status'] ?? PksFieldActivity::STATUS_DRAFT,
            'recorded_by' => auth()->id(),
            'violation_id' => $validated['violation_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Get assignment to redirect back to schedule
        $assignment = PksDutyAssignment::find($validated['pks_duty_assignment_id']);

        return redirect()->route('pks-duty-schedules.show', $assignment->pks_duty_schedule_id)
            ->with('success', 'Aktivitas lapangan berhasil disimpan.');
    }

    /**
     * Display the specified PKS field activity.
     */
    public function show(PksFieldActivity $pksFieldActivity): View
    {
        $this->authorize('view', $pksFieldActivity);

        $pksFieldActivity->load([
            'assignment.member.student',
            'assignment.member.student.schoolClass',
            'assignment.schedule.shift',
            'assignment.location',
            'recordedBy',
            'violation.student',
            'violation.violationType',
            'school',
        ]);

        return view('pks-field-activities.show', compact('pksFieldActivity'));
    }

    /**
     * Show the form for editing the specified PKS field activity.
     */
    public function edit(PksFieldActivity $pksFieldActivity): View
    {
        $this->authorize('update', $pksFieldActivity);

        $schoolId = auth()->user()->school_id;

        $pksFieldActivity->load([
            'assignment.member.student',
            'assignment.schedule.shift',
            'assignment.location',
        ]);

        // Get active violations for linking
        $violations = Violation::query()
            ->when($schoolId, fn ($q) => $q->where('school_id', $schoolId))
            ->whereIn('status', ['recorded', 'verified'])
            ->with('student')
            ->orderBy('occurred_at', 'desc')
            ->limit(50)
            ->get();

        $activityTypes = PksFieldActivity::TYPE_LABELS;

        return view('pks-field-activities.edit', compact(
            'pksFieldActivity',
            'violations',
            'activityTypes'
        ));
    }

    /**
     * Update the specified PKS field activity.
     */
    public function update(UpdatePksFieldActivityRequest $request, PksFieldActivity $pksFieldActivity): RedirectResponse
    {
        $this->authorize('update', $pksFieldActivity);

        $validated = $request->validated();

        $pksFieldActivity->update($validated);

        return redirect()->route('pks-field-activities.show', $pksFieldActivity)
            ->with('success', 'Aktivitas lapangan berhasil diperbarui.');
    }

    /**
     * Cancel the specified PKS field activity.
     */
    public function cancel(Request $request, PksFieldActivity $pksFieldActivity): RedirectResponse
    {
        $this->authorize('cancel', $pksFieldActivity);

        $pksFieldActivity->update([
            'status' => PksFieldActivity::STATUS_CANCELLED,
            'notes' => $pksFieldActivity->notes . "\n[Dibatalkan pada " . now()->format('d/m/Y H:i') . " oleh " . auth()->user()->name . "]",
        ]);

        return redirect()->back()
            ->with('success', 'Aktivitas lapangan berhasil dibatalkan.');
    }

    /**
     * Get school ID.
     */
    protected function getSchoolId(): int
    {
        return auth()->user()->school_id ?? 0;
    }
}
