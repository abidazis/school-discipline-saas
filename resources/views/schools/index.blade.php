<x-app-layout>
    <x-slot name="title">Sekolah</x-slot>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <div class="page-title-icon" style="background: #e0e7ff; color: #4f46e5;">
                <i class="bi bi-building"></i>
            </div>
            Sekolah
        </h1>
        <a href="{{ route('schools.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>Tambah Sekolah
        </a>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('schools.index') }}" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group filter-group-search">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search"
                                   placeholder="Cari sekolah..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="filter-group">
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="filter-group filter-group-actions">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-filter"></i>Filter
                        </button>
                        <a href="{{ route('schools.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
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
                        <th data-label="Nama">Nama</th>
                        <th data-label="Kontak">Kontak</th>
                        <th data-label="Users">Users</th>
                        <th data-label="Status">Status</th>
                        <th data-label="Aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                        <tr>
                            <td data-label="Nama">
                                <a href="{{ route('schools.show', $school) }}" class="text-decoration-none fw-medium">
                                    {{ $school->name }}
                                </a>
                                <br>
                                <small class="text-muted">{{ $school->slug }}</small>
                            </td>
                            <td data-label="Kontak">
                                <div class="small">
                                    @if($school->email)
                                        <div><i class="bi bi-envelope me-1"></i>{{ $school->email }}</div>
                                    @endif
                                    @if($school->phone)
                                        <div><i class="bi bi-telephone me-1"></i>{{ $school->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td data-label="Users">
                                <span class="badge badge-secondary">{{ $school->users()->count() }}</span>
                            </td>
                            <td data-label="Status">
                                @if($school->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td data-label="Aksi">
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
                <div class="pagination-wrapper">
                    {{ $schools->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
