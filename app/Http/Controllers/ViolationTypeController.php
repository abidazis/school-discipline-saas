<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreViolationTypeRequest;
use App\Http\Requests\UpdateViolationTypeRequest;
use App\Models\ViolationType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ViolationTypeController extends Controller
{
    /**
     * Display a listing of violation types.
     */
    public function index(Request $request): View
    {
        $query = ViolationType::query()->with('school');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $violationTypes = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('violation-types.index', compact('violationTypes'));
    }

    /**
     * Show the form for creating a new violation type.
     */
    public function create(): View
    {
        return view('violation-types.create');
    }

    /**
     * Store a newly created violation type.
     */
    public function store(StoreViolationTypeRequest $request): RedirectResponse
    {
        ViolationType::create($request->validated());

        return redirect()->route('violation-types.index')
            ->with('success', 'Jenis pelanggaran berhasil ditambahkan.');
    }

    /**
     * Display the specified violation type.
     */
    public function show(ViolationType $violationType): View
    {
        $violationType->load('violations.student');

        return view('violation-types.show', compact('violationType'));
    }

    /**
     * Show the form for editing the specified violation type.
     */
    public function edit(ViolationType $violationType): View
    {
        return view('violation-types.edit', compact('violationType'));
    }

    /**
     * Update the specified violation type.
     */
    public function update(UpdateViolationTypeRequest $request, ViolationType $violationType): RedirectResponse
    {
        $violationType->update($request->validated());

        return redirect()->route('violation-types.show', $violationType)
            ->with('success', 'Jenis pelanggaran berhasil diperbarui.');
    }

    /**
     * Remove the specified violation type.
     */
    public function destroy(ViolationType $violationType): RedirectResponse
    {
        if ($violationType->isUsed()) {
            return redirect()->route('violation-types.show', $violationType)
                ->with('error', 'Jenis pelanggaran sudah digunakan dalam riwayat. Nonaktifkan saja.');
        }

        $violationType->delete();

        return redirect()->route('violation-types.index')
            ->with('success', 'Jenis pelanggaran berhasil dihapus.');
    }
}
