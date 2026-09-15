<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-shield-check text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">Welcome back, {{ auth()->user()->name }}!</h5>
                            <p class="mb-0 text-muted small">
                                @if(auth()->user()->isSuperAdmin())
                                    You're logged in as Super Admin. You have access to all schools.
                                @elseif(auth()->user()->school)
                                    Managing {{ auth()->user()->school->name }}
                                @else
                                    Please contact an administrator to assign your school.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-mortarboard text-secondary fs-4"></i>
                    </div>
                    <h3 class="mb-1">-</h3>
                    <p class="text-muted mb-0">Students</p>
                    <span class="badge bg-secondary mt-2">Coming Soon</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                    </div>
                    <h3 class="mb-1">-</h3>
                    <p class="text-muted mb-0">Violations</p>
                    <span class="badge bg-secondary mt-2">Coming Soon</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-file-text text-info fs-4"></i>
                    </div>
                    <h3 class="mb-1">-</h3>
                    <p class="text-muted mb-0">PKS Letters</p>
                    <span class="badge bg-secondary mt-2">Coming Soon</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Quick Info</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-5 text-muted">Role</dt>
                                <dd class="col-7">
                                    <span class="badge bg-{{ auth()->user()->isSuperAdmin() ? 'danger' : 'primary' }}">
                                        {{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}
                                    </span>
                                </dd>
                                <dt class="col-5 text-muted">Email</dt>
                                <dd class="col-7">{{ auth()->user()->email }}</dd>
                                <dt class="col-5 text-muted">School</dt>
                                <dd class="col-7">
                                    @if(auth()->user()->school)
                                        {{ auth()->user()->school->name }}
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                        <div class="col-12 col-md-6">
                            <p class="text-muted small mb-2">
                                This is Phase 1 of the School Discipline Management System.
                                Additional features will be available in future phases.
                            </p>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-person me-1"></i>Edit Profile
                                </a>
                                @if(auth()->user()->isSuperAdmin())
                                    <a href="{{ route('schools.index') }}" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-building me-1"></i>Manage Schools
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
