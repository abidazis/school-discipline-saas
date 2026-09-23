<x-app-layout>
    <x-slot name="title">{{ $school->name }}</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('schools.index') }}">Schools</a></li>
            <li class="breadcrumb-item active">{{ $school->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 mb-1">
                <i class="bi bi-building me-2"></i>{{ $school->name }}
            </h1>
            @if($school->is_active)
                <span class="badge badge-success">Active</span>
            @else
                <span class="badge badge-secondary">Inactive</span>
            @endif
        </div>
        <div class="btn-group">
            <a href="{{ route('schools.edit', $school) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="bi bi-trash me-1"></i>Delete
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- School Info -->
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">School Information</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted small">Slug</dt>
                        <dd class="mb-3"><code>{{ $school->slug }}</code></dd>

                        <dt class="text-muted small">Email</dt>
                        <dd class="mb-3">{{ $school->email ?? '<span class="text-muted">Not set</span>' }}</dd>

                        <dt class="text-muted small">Phone</dt>
                        <dd class="mb-3">{{ $school->phone ?? '<span class="text-muted">Not set</span>' }}</dd>

                        <dt class="text-muted small">Address</dt>
                        <dd class="mb-0">{{ $school->address ?? '<span class="text-muted">Not set</span>' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Users -->
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Users ({{ $school->users->count() }})</h5>
                    <a href="{{ route('users.create', ['school_id' => $school->id]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Add User
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($school->users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge {{ $user->isSuperAdmin() ? 'badge badge-danger' : 'badge badge-primary' }}">
                                            {{ ucwords(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        No users assigned to this school.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete School</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $school->name }}</strong>?</p>
                    @if($school->users()->count() > 0)
                        <div class="alert alert-warning py-2">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            This school has {{ $school->users()->count() }} users. Please remove or reassign them first.
                        </div>
                    @else
                        <p class="text-muted small">This action cannot be undone.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    @if($school->users()->count() === 0)
                        <form method="POST" action="{{ route('schools.destroy', $school) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete School</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
