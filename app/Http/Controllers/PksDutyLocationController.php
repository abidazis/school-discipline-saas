<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePksDutyLocationRequest;
use App\Http\Requests\UpdatePksDutyLocationRequest;
use App\Models\PksDutyLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PksDutyLocationController extends Controller
{
    /**
     * Display a listing of PKS duty locations.
     */
    public function index(Request $request): View
    {
        $query = PksDutyLocation::query()->withCount('dutySchedules');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $locations = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('pks-duty-locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new PKS duty location.
     */
    public function create(): View
    {
        $this->authorize('create', PksDutyLocation::class);

        return view('pks-duty-locations.create');
    }

    /**
     * Store a newly created PKS duty location.
     */
    public function store(StorePksDutyLocationRequest $request): RedirectResponse
    {
        $this->authorize('create', PksDutyLocation::class);

        PksDutyLocation::create($request->validated());

        return redirect()->route('pks-duty-locations.index')
            ->with('success', 'Lokasi piket berhasil ditambahkan.');
    }

    /**
     * Display the specified PKS duty location.
     */
    public function show(PksDutyLocation $pksDutyLocation): View
    {
        $this->authorize('view', $pksDutyLocation);

        $pksDutyLocation->loadCount('dutySchedules');

        return view('pks-duty-locations.show', compact('pksDutyLocation'));
    }

    /**
     * Show the form for editing the specified PKS duty location.
     */
    public function edit(PksDutyLocation $pksDutyLocation): View
    {
        $this->authorize('update', $pksDutyLocation);

        return view('pks-duty-locations.edit', compact('pksDutyLocation'));
    }

    /**
     * Update the specified PKS duty location.
     */
    public function update(UpdatePksDutyLocationRequest $request, PksDutyLocation $pksDutyLocation): RedirectResponse
    {
        $this->authorize('update', $pksDutyLocation);

        $pksDutyLocation->update($request->validated());

        return redirect()->route('pks-duty-locations.show', $pksDutyLocation)
            ->with('success', 'Lokasi piket berhasil diperbarui.');
    }
}
