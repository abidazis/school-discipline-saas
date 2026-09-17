<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index(Request $request): View
    {
        $query = Department::query()->with('school');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $departments = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create(): View
    {
        abort_unless(
            auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin(),
            403
        );

        return view('departments.create');
    }

    /**
     * Store a newly created department.
     */
    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        Department::create($request->validated());

        return redirect()->route('departments.index')
            ->with('success', 'Program keahlian berhasil ditambahkan.');
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department): View
    {
        $department->load(['schoolClasses', 'school']);

        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department): View
    {
        return view('departments.edit', compact('department'));
    }

    /**
     * Update the specified department.
     */
    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        return redirect()->route('departments.show', $department)
            ->with('success', 'Program keahlian berhasil diperbarui.');
    }

    /**
     * Remove the specified department.
     */
    public function destroy(Department $department): RedirectResponse
    {
        if ($department->schoolClasses()->exists()) {
            return redirect()->route('departments.show', $department)
                ->with('error', 'Tidak dapat menghapus program keahlian yang memiliki kelas. Nonaktifkan saja.');
        }

        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Program keahlian berhasil dihapus.');
    }
}
