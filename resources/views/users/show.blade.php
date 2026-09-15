<x-app-layout>
    <x-slot name="title">{{ $user->name }}</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">{{ $user->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 mb-1">
                <i class="bi bi-person me-2"></i>{{ $user->name }}
            </h1>
            <span class="badge bg-{{ $user->isSuperAdmin() ? 'danger' : 'primary' }}">
                {{ ucwords(str_replace('_', ' ', $user->role)) }}
            </span>
        </div>
        <div class="btn-group">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            @if($user->id !== auth()->id())
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            @endif
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
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted small">Email</dt>
                        <dd class="mb-3">{{ $user->email }}</dd>

                        <dt class="text-muted small">Role</dt>
                        <dd class="mb-3">
                            <span class="badge bg-{{ $user->isSuperAdmin() ? 'danger' : 'primary' }}">
                                {{ ucwords(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </dd>

                        <dt class="text-muted small">School</dt>
                        <dd class="mb-3">
                            @if($user->school)
                                <a href="{{ route('schools.show', $user->school) }}">
                                    {{ $user->school->name }}
                                </a>
                            @else
                                <span class="text-muted">Not assigned</span>
                            @endif
                        </dd>

                        <dt class="text-muted small">Email Verified</dt>
                        <dd class="mb-0">
                            @if($user->email_verified_at)
                                <span class="text-success"><i class="bi bi-check-circle me-1"></i>{{ $user->email_verified_at->format('M d, Y H:i') }}</span>
                            @else
                                <span class="text-muted"><i class="bi bi-x-circle me-1"></i>Not verified</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Activity</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted small">Created</dt>
                        <dd class="mb-3">{{ $user->created_at->format('M d, Y H:i') }}</dd>

                        <dt class="text-muted small">Last Updated</dt>
                        <dd class="mb-0">{{ $user->updated_at->format('M d, Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    @if($user->id !== auth()->id())
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong>{{ $user->name }}</strong>?</p>
                        <p class="text-muted small">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form method="POST" action="{{ route('users.destroy', $user) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
