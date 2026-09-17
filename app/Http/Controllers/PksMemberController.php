<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePksMemberRequest;
use App\Http\Requests\UpdatePksMemberRequest;
use App\Models\PksMember;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PksMemberController extends Controller
{
    /**
     * Display a listing of PKS members.
     */
    public function index(Request $request): View
    {
        $query = PksMember::query()
            ->with(['student.schoolClass.department']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        if ($request->filled('school_class_id')) {
            $query->whereHas('student', fn ($q) => $q->where('school_class_id', $request->school_class_id));
        }

        if ($request->filled('department_id')) {
            $query->whereHas('student.schoolClass', fn ($q) => $q->where('department_id', $request->department_id));
        }

        $pksMembers = $query->orderBy('joined_at', 'desc')->paginate(15)->withQueryString();

        // Get filter options
        $classes = SchoolClass::query()
            ->when(auth()->user()->school_id, fn ($q) => $q->where('school_id', auth()->user()->school_id))
            ->orderBy('name')
            ->get();

        $positions = [
            PksMember::POSITION_MEMBER,
            PksMember::POSITION_KORLAP,
            PksMember::POSITION_WAKIL_KORLAP,
            PksMember::POSITION_KETUA,
            PksMember::POSITION_WAKIL_KETUA,
        ];

        return view('pks-members.index', compact('pksMembers', 'classes', 'positions'));
    }

    /**
     * Show the form for creating a new PKS member.
     */
    public function create(): View
    {
        $positions = [
            PksMember::POSITION_MEMBER,
            PksMember::POSITION_KORLAP,
            PksMember::POSITION_WAKIL_KORLAP,
            PksMember::POSITION_KETUA,
            PksMember::POSITION_WAKIL_KETUA,
        ];

        return view('pks-members.create', compact('positions'));
    }

    /**
     * Store a newly created PKS member.
     */
    public function store(StorePksMemberRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Check for duplicate active membership
        $student = Student::findOrFail($validated['student_id']);

        if (PksMember::hasActiveMembership($student->id, $student->school_id)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Siswa ini sudah menjadi anggota PKS aktif.');
        }

        PksMember::create($validated);

        return redirect()->route('pks-members.index')
            ->with('success', 'Anggota PKS berhasil ditambahkan.');
    }

    /**
     * Display the specified PKS member.
     */
    public function show(PksMember $pksMember): View
    {
        $pksMember->load(['student.schoolClass.department', 'student.academicYear']);

        return view('pks-members.show', compact('pksMember'));
    }

    /**
     * Show the form for editing the specified PKS member.
     */
    public function edit(PksMember $pksMember): View
    {
        $positions = [
            PksMember::POSITION_MEMBER,
            PksMember::POSITION_KORLAP,
            PksMember::POSITION_WAKIL_KORLAP,
            PksMember::POSITION_KETUA,
            PksMember::POSITION_WAKIL_KETUA,
        ];

        return view('pks-members.edit', compact('pksMember', 'positions'));
    }

    /**
     * Update the specified PKS member.
     */
    public function update(UpdatePksMemberRequest $request, PksMember $pksMember): RedirectResponse
    {
        $validated = $request->validated();

        // If changing to active status, check for duplicate
        if (isset($validated['status']) &&
            $validated['status'] === PksMember::STATUS_ACTIVE &&
            $pksMember->status !== PksMember::STATUS_ACTIVE) {

            if (PksMember::where('student_id', $pksMember->student_id)
                ->where('school_id', $pksMember->school_id)
                ->where('id', '!=', $pksMember->id)
                ->where('status', PksMember::STATUS_ACTIVE)
                ->exists()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Siswa ini sudah menjadi anggota PKS aktif.');
            }
        }

        $pksMember->update($validated);

        return redirect()->route('pks-members.show', $pksMember)
            ->with('success', 'Data anggota PKS berhasil diperbarui.');
    }
}
