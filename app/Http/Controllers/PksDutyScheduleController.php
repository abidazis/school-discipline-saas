<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePksDutyScheduleRequest;
use App\Http\Requests\UpdatePksDutyScheduleRequest;
use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PksDutyScheduleController extends Controller
{
    /**
     * Display a listing of PKS duty schedules.
     */
    public function index(Request $request): View
    {
        $query = PksDutySchedule::query()
            ->with(['shift', 'locations', 'school']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('locations', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('pks_shift_id')) {
            $query->where('pks_shift_id', $request->pks_shift_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('schedule_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('schedule_date', '<=', $request->date_to);
        }

        $schedules = $query->orderBy('schedule_date', 'desc')->paginate(15)->withQueryString();

        $shifts = PksShift::active()
            ->when(auth()->user()->school_id, fn ($q) => $q->where('school_id', auth()->user()->school_id))
            ->orderBy('start_time')
            ->get();

        return view('pks-duty-schedules.index', compact('schedules', 'shifts'));
    }

    /**
     * Show the form for creating a new PKS duty schedule.
     */
    public function create(): View
    {
        $this->authorize('create', PksDutySchedule::class);

        $shifts = PksShift::active()
            ->when(auth()->user()->school_id, fn ($q) => $q->where('school_id', auth()->user()->school_id))
            ->orderBy('start_time')
            ->get();

        $locations = PksDutyLocation::active()
            ->when(auth()->user()->school_id, fn ($q) => $q->where('school_id', auth()->user()->school_id))
            ->orderBy('name')
            ->get();

        return view('pks-duty-schedules.create', compact('shifts', 'locations'));
    }

    /**
     * Store a newly created PKS duty schedule.
     */
    public function store(StorePksDutyScheduleRequest $request): RedirectResponse
    {
        $this->authorize('create', PksDutySchedule::class);

        $validated = $request->validated();

        // Use transaction for schedule + location attachment
        $schedule = DB::transaction(function () use ($validated) {
            $schedule = PksDutySchedule::create([
                'school_id' => $this->getSchoolId($validated),
                'pks_shift_id' => $validated['pks_shift_id'],
                'schedule_date' => $validated['schedule_date'],
                'status' => $validated['status'] ?? PksDutySchedule::STATUS_SCHEDULED,
                'notes' => $validated['notes'] ?? null,
            ]);

            $schedule->locations()->sync($validated['location_ids']);

            return $schedule;
        });

        return redirect()->route('pks-duty-schedules.show', $schedule)
            ->with('success', 'Jadwal piket berhasil ditambahkan.');
    }

    /**
     * Display the specified PKS duty schedule.
     */
    public function show(PksDutySchedule $pksDutySchedule): View
    {
        $this->authorize('view', $pksDutySchedule);

        $pksDutySchedule->load(['shift', 'locations', 'school']);

        return view('pks-duty-schedules.show', compact('pksDutySchedule'));
    }

    /**
     * Show the form for editing the specified PKS duty schedule.
     */
    public function edit(PksDutySchedule $pksDutySchedule): View
    {
        $this->authorize('update', $pksDutySchedule);

        $shifts = PksShift::active()
            ->when(auth()->user()->school_id, fn ($q) => $q->where('school_id', auth()->user()->school_id))
            ->orderBy('start_time')
            ->get();

        $locations = PksDutyLocation::active()
            ->when(auth()->user()->school_id, fn ($q) => $q->where('school_id', auth()->user()->school_id))
            ->orderBy('name')
            ->get();

        return view('pks-duty-schedules.edit', compact('pksDutySchedule', 'shifts', 'locations'));
    }

    /**
     * Update the specified PKS duty schedule.
     */
    public function update(UpdatePksDutyScheduleRequest $request, PksDutySchedule $pksDutySchedule): RedirectResponse
    {
        $this->authorize('update', $pksDutySchedule);

        $validated = $request->validated();

        DB::transaction(function () use ($pksDutySchedule, $validated) {
            $pksDutySchedule->update([
                'pks_shift_id' => $validated['pks_shift_id'],
                'schedule_date' => $validated['schedule_date'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $pksDutySchedule->locations()->sync($validated['location_ids']);
        });

        return redirect()->route('pks-duty-schedules.show', $pksDutySchedule)
            ->with('success', 'Jadwal piket berhasil diperbarui.');
    }

    /**
     * Get school ID for validation.
     */
    protected function getSchoolId(array $validated): int
    {
        if (auth()->user()->isSuperAdmin()) {
            // For super admin, get school from shift
            return PksShift::findOrFail($validated['pks_shift_id'])->school_id;
        }

        return auth()->user()->school_id ?? 0;
    }
}
