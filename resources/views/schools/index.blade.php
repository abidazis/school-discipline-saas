<x-app-layout>
    <x-slot name="title">Sekolah</x-slot>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <div class="page-title-icon" style="background: var(--color-primary-light); color: var(--color-primary);">
                <i class="bi bi-building"></i>
            </div>
            Sekolah
        </h1>
        <a href="{{ route('schools.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Sekolah
        </a>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('schools.index') }}" class="row g-3">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Cari sekolah..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('schools.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- List Card -->
    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th>Aksi</th>
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
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('schools.show', $school) }}" class="btn btn-sm btn-outline-primary" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('schools.edit', $school) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <div class="empty-state-title">Belum ada sekolah</div>
                                    <div class="empty-state-text">Tambahkan sekolah untuk mulai menggunakan sistem.</div>
                                    <a href="{{ route('schools.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i>Tambah Sekolah
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($schools->hasPages())
            <div class="card-footer">
                {{ $schools->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
