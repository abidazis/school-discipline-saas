<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of academic years.
     */
    public function index(Request $request): View
    {
        $query = AcademicYear::query()->with('school');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $academicYears = $query->orderBy('name', 'desc')->paginate(10)->withQueryString();

        return view('academic-years.index', compact('academicYears'));
    }

    /**
     * Show the form for creating a new academic year.
     */
    public function create(): View
    {
        abort_unless(
            auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin(),
            403
        );

        return view('academic-years.create');
    }

    /**
     * Store a newly created academic year.
     */
    public function store(StoreAcademicYearRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request) {
            // If this year is being set as active, deactivate others
            if (!empty($validated['is_active'])) {
                AcademicYear::withoutTenantScope()
                    ->where('school_id', $request->user()->school_id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            AcademicYear::create($validated);
        });

        return redirect()->route('academic-years.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /**
     * Display the specified academic year.
     */
    public function show(AcademicYear $academicYear): View
    {
        $academicYear->load(['schoolClasses', 'students']);

        return view('academic-years.show', compact('academicYear'));
    }

    /**
     * Show the form for editing the specified academic year.
     */
    public function edit(AcademicYear $academicYear): View
    {
        return view('academic-years.edit', compact('academicYear'));
    }

    /**
     * Update the specified academic year.
     */
    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request, $academicYear) {
            // If this year is being set as active, deactivate others
            if (!empty($validated['is_active']) && !$academicYear->is_active) {
                AcademicYear::withoutTenantScope()
                    ->where('school_id', $academicYear->school_id)
                    ->where('is_active', true)
                    ->where('id', '!=', $academicYear->id)
                    ->update(['is_active' => false]);
            }

            $academicYear->update($validated);
        });

        return redirect()->route('academic-years.show', $academicYear)
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified academic year.
     */
    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        if ($academicYear->schoolClasses()->exists()) {
            return redirect()->route('academic-years.show', $academicYear)
                ->with('error', 'Tidak dapat menghapus tahun ajaran yang memiliki kelas.');
        }

        if ($academicYear->students()->exists()) {
            return redirect()->route('academic-years.show', $academicYear)
                ->with('error', 'Tidak dapat menghapus tahun ajaran yang memiliki siswa.');
        }

        $academicYear->delete();

        return redirect()->route('academic-years.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
