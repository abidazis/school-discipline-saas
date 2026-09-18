<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePksShiftRequest;
use App\Http\Requests\UpdatePksShiftRequest;
use App\Models\PksShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PksShiftController extends Controller
{
    /**
     * Display a listing of PKS shifts.
     */
    public function index(Request $request): View
    {
        $query = PksShift::query()->withCount('dutySchedules');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $shifts = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('pks-shifts.index', compact('shifts'));
    }

    /**
     * Show the form for creating a new PKS shift.
     */
    public function create(): View
    {
        $this->authorize('create', PksShift::class);

        return view('pks-shifts.create');
    }

    /**
     * Store a newly created PKS shift.
     */
    public function store(StorePksShiftRequest $request): RedirectResponse
    {
        $this->authorize('create', PksShift::class);

        PksShift::create($request->validated());

        return redirect()->route('pks-shifts.index')
            ->with('success', 'Shift piket berhasil ditambahkan.');
    }

    /**
     * Display the specified PKS shift.
     */
    public function show(PksShift $pksShift): View
    {
        $this->authorize('view', $pksShift);

        $pksShift->loadCount('dutySchedules');

        return view('pks-shifts.show', compact('pksShift'));
    }

    /**
     * Show the form for editing the specified PKS shift.
     */
    public function edit(PksShift $pksShift): View
    {
        $this->authorize('update', $pksShift);

        return view('pks-shifts.edit', compact('pksShift'));
    }

    /**
     * Update the specified PKS shift.
     */
    public function update(UpdatePksShiftRequest $request, PksShift $pksShift): RedirectResponse
    {
        $this->authorize('update', $pksShift);

        $pksShift->update($request->validated());

        return redirect()->route('pks-shifts.show', $pksShift)
            ->with('success', 'Shift piket berhasil diperbarui.');
    }
}
