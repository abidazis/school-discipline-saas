<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of school classes.
     */
    public function index(Request $request): View
    {
        $query = SchoolClass::query()
            ->with(['academicYear', 'department', 'school']);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $schoolClasses = $query->orderBy('grade_level')->orderBy('name')->paginate(10)->withQueryString();

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $departments = Department::active()->orderBy('name')->get();

        return view('classes.index', compact('schoolClasses', 'academicYears', 'departments'));
    }

    /**
     * Show the form for creating a new school class.
     */
    public function create(Request $request): View
    {
        abort_unless(
            auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin(),
            403
        );

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $departments = Department::active()->orderBy('name')->get();
        $selectedAcademicYear = $request->filled('academic_year_id')
            ? AcademicYear::find($request->academic_year_id)
            : null;

        return view('classes.create', compact('academicYears', 'departments', 'selectedAcademicYear'));
    }

    /**
     * Store a newly created school class.
     */
    public function store(StoreSchoolClassRequest $request): RedirectResponse
    {
        SchoolClass::create($request->validated());

        return redirect()->route('classes.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified school class.
     */
    public function show(SchoolClass $schoolClass): View
    {
        $schoolClass->load(['academicYear', 'department', 'students', 'school']);

        return view('classes.show', compact('schoolClass'));
    }

    /**
     * Show the form for editing the specified school class.
     */
    public function edit(SchoolClass $schoolClass): View
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $departments = Department::orderBy('name')->get();

        return view('classes.edit', compact('schoolClass', 'academicYears', 'departments'));
    }

    /**
     * Update the specified school class.
     */
    public function update(UpdateSchoolClassRequest $request, SchoolClass $schoolClass): RedirectResponse
    {
        $schoolClass->update($request->validated());

        return redirect()->route('classes.show', $schoolClass)
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified school class.
     */
    public function destroy(SchoolClass $schoolClass): RedirectResponse
    {
        if ($schoolClass->students()->exists()) {
            return redirect()->route('classes.show', $schoolClass)
                ->with('error', 'Tidak dapat menghapus kelas yang memiliki siswa. Nonaktifkan saja.');
        }

        $schoolClass->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
