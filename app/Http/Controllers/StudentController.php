<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request): View
    {
        $query = Student::query()
            ->with(['academicYear', 'schoolClass.department', 'school']);

        // Search by NIS, NISN, or name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        // Filter by academic year
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Filter by class
        if ($request->filled('school_class_id')) {
            $query->where('school_class_id', $request->school_class_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $students = $query->orderBy('full_name')->paginate(15)->withQueryString();

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        // Get classes for filter dropdown
        $classesQuery = SchoolClass::query()->with('department');
        if ($request->filled('academic_year_id')) {
            $classesQuery->where('academic_year_id', $request->academic_year_id);
        }
        $classes = $classesQuery->orderBy('grade_level')->orderBy('name')->get();

        return view('students.index', compact('students', 'academicYears', 'classes'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create(Request $request): View
    {
        abort_unless(
            auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator(),
            403
        );

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $selectedAcademicYear = $request->filled('academic_year_id')
            ? AcademicYear::find($request->academic_year_id)
            : $academicYears->firstWhere('is_active', true) ?? $academicYears->first();

        $classes = SchoolClass::query()
            ->when($selectedAcademicYear, function ($q) use ($selectedAcademicYear) {
                $q->where('academic_year_id', $selectedAcademicYear->id);
            })
            ->with('department')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        return view('students.create', compact('academicYears', 'classes', 'selectedAcademicYear'));
    }

    /**
     * Store a newly created student.
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        Student::create($request->validated());

        return redirect()->route('students.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student): View
    {
        $student->load(['academicYear', 'schoolClass.department', 'school']);

        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student): View
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $classes = SchoolClass::with('department')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        return view('students.edit', compact('student', 'academicYears', 'classes'));
    }

    /**
     * Update the specified student.
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $student->update($request->validated());

        return redirect()->route('students.show', $student)
            ->with('success', 'Siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }
}
