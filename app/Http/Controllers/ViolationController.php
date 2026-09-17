<?php

namespace App\Http\Controllers;

use App\Http\Requests\CancelViolationRequest;
use App\Http\Requests\StoreViolationRequest;
use App\Http\Requests\UpdateViolationRequest;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationEvidence;
use App\Models\ViolationType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ViolationController extends Controller
{
    /**
     * Display a listing of violations.
     */
    public function index(Request $request): View
    {
        $query = Violation::query()
            ->with(['student', 'violationType', 'officer', 'academicYear', 'schoolClass.department']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('full_name', 'like', "%{$search}%")
                       ->orWhere('nis', 'like', "%{$search}%");
                })
                ->orWhereHas('violationType', function ($vq) use ($search) {
                    $vq->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('school_class_id')) {
            $query->where('school_class_id', $request->school_class_id);
        }

        if ($request->filled('violation_type_id')) {
            $query->where('violation_type_id', $request->violation_type_id);
        }

        if ($request->filled('category')) {
            $query->whereHas('violationType', fn ($q) => $q->where('category', $request->category));
        }

        if ($request->filled('severity')) {
            $query->whereHas('violationType', fn ($q) => $q->where('severity', $request->severity));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('officer_id')) {
            $query->where('officer_id', $request->officer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('occurred_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('occurred_at', '<=', $request->date_to);
        }

        $violations = $query->orderBy('occurred_at', 'desc')->paginate(15)->withQueryString();

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $violationTypes = ViolationType::active()->orderBy('name')->get();
        $officers = User::whereIn('role', [User::ROLE_SCHOOL_ADMIN, User::ROLE_OPERATOR, User::ROLE_TEACHER])
            ->when(auth()->user()->school_id, fn ($q) => $q->where('school_id', auth()->user()->school_id))
            ->orderBy('name')->get();

        return view('violations.index', compact(
            'violations',
            'academicYears',
            'violationTypes',
            'officers'
        ));
    }

    /**
     * Show the form for creating a new violation.
     */
    public function create(Request $request): View
    {
        $violationTypes = ViolationType::active()->orderBy('category')->orderBy('name')->get();

        return view('violations.create', compact('violationTypes'));
    }

    /**
     * Store a newly created violation.
     */
    public function store(StoreViolationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $student = Student::with('schoolClass')->findOrFail($validated['student_id']);
        $violationType = ViolationType::findOrFail($validated['violation_type_id']);

        // Use transaction for data integrity
        $violation = DB::transaction(function () use ($validated, $student, $violationType) {
            $violation = Violation::create([
                'school_id' => $student->school_id,
                'student_id' => $student->id,
                'violation_type_id' => $violationType->id,
                'academic_year_id' => $student->academic_year_id,
                'school_class_id' => $student->school_class_id,
                'officer_id' => auth()->id(),
                'occurred_at' => $validated['occurred_at'],
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'] ?? null,
                'points' => $violationType->points,
                'status' => Violation::STATUS_RECORDED,
            ]);

            // Handle file uploads
            if (!empty($validated['evidences'])) {
                $this->storeEvidences($violation, $validated['evidences']);
            }

            return $violation;
        });

        return redirect()->route('violations.show', $violation)
            ->with('success', 'Pelanggaran berhasil dicatat.');
    }

    /**
     * Display the specified violation.
     */
    public function show(Violation $violation): View
    {
        $violation->load(['student.schoolClass.department', 'violationType', 'officer', 'academicYear', 'evidences']);

        return view('violations.show', compact('violation'));
    }

    /**
     * Show the form for editing the specified violation.
     */
    public function edit(Violation $violation): View|RedirectResponse
    {
        if (!$violation->canEdit()) {
            return redirect()->route('violations.show', $violation)
                ->with('error', 'Pelanggaran yang sudah diverifikasi atau dibatalkan tidak dapat diedit.');
        }

        $violationTypes = ViolationType::active()->orderBy('category')->orderBy('name')->get();

        return view('violations.edit', compact('violation', 'violationTypes'));
    }

    /**
     * Update the specified violation.
     */
    public function update(UpdateViolationRequest $request, Violation $violation): RedirectResponse
    {
        if (!$violation->canEdit()) {
            return redirect()->route('violations.show', $violation)
                ->with('error', 'Pelanggaran tidak dapat diedit.');
        }

        $validated = $request->validated();

        if (empty($validated)) {
            return redirect()->route('violations.show', $violation);
        }

        DB::transaction(function () use ($violation, $validated) {
            $violation->update([
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            if (!empty($validated['evidences'])) {
                $this->storeEvidences($violation, $validated['evidences']);
            }
        });

        return redirect()->route('violations.show', $violation)
            ->with('success', 'Pelanggaran berhasil diperbarui.');
    }

    /**
     * Verify the specified violation.
     */
    public function verify(Violation $violation): RedirectResponse
    {
        // Check policy authorization
        if (auth()->user()->cannot('verify', $violation)) {
            abort(403);
        }

        if (!$violation->canVerify()) {
            return redirect()->route('violations.show', $violation)
                ->with('error', 'Pelanggaran tidak dapat diverifikasi.');
        }

        $violation->verify();

        return redirect()->route('violations.show', $violation)
            ->with('success', 'Pelanggaran berhasil diverifikasi.');
    }

    /**
     * Cancel the specified violation.
     */
    public function cancel(CancelViolationRequest $request, Violation $violation): RedirectResponse
    {
        // Check policy authorization
        if (auth()->user()->cannot('cancel', $violation)) {
            abort(403);
        }

        if (!$violation->canCancel()) {
            return redirect()->route('violations.show', $violation)
                ->with('error', 'Pelanggaran tidak dapat dibatalkan.');
        }

        $violation->cancel($request->reason);

        return redirect()->route('violations.show', $violation)
            ->with('success', 'Pelanggaran berhasil dibatalkan.');
    }

    /**
     * Delete evidence file.
     */
    public function destroyEvidence(ViolationEvidence $evidence): RedirectResponse
    {
        $violation = $evidence->violation;

        // Check authorization
        if ($evidence->violation->school_id !== auth()->user()->school_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $evidence->delete();

        return redirect()->route('violations.show', $violation)
            ->with('success', 'Bukti berhasil dihapus.');
    }

    /**
     * Store evidence files for a violation.
     */
    protected function storeEvidences(Violation $violation, array $files): void
    {
        foreach ($files as $file) {
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = "violations/{$violation->school_id}/{$violation->id}";

            Storage::disk('public')->putFileAs($path, $file, $filename);

            ViolationEvidence::create([
                'violation_id' => $violation->id,
                'file_path' => "{$path}/{$filename}",
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
    }
}
