<x-app-layout>
    <x-slot name="title">Schools</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0"><i class="bi bi-building me-2"></i>Schools</h1>
        <a href="{{ route('schools.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Add School
        </a>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('schools.index') }}" class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Search schools..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                    <a href="{{ route('schools.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Schools List -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                        <tr>
                            <td>
                                <a href="{{ route('schools.show', $school) }}" class="text-decoration-none fw-medium">
                                    {{ $school->name }}
                                </a>
                                <br>
                                <small class="text-muted">{{ $school->slug }}</small>
                            </td>
                            <td>
                                <div class="small">
                                    @if($school->email)
                                        <div><i class="bi bi-envelope me-1"></i>{{ $school->email }}</div>
                                    @endif
                                    @if($school->phone)
                                        <div><i class="bi bi-telephone me-1"></i>{{ $school->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $school->users()->count() }}</span>
                            </td>
                            <td>
                                @if($school->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('schools.show', $school) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('schools.edit', $school) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No schools found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($schools->hasPages())
            <div class="card-footer bg-white">
                {{ $schools->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
