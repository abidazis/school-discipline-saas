<x-app-layout>
    <x-slot name="title">Program Keahlian</x-slot>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <div class="page-title-icon">
                <i class="bi bi-diagram-3"></i>
            </div>
            Program Keahlian
        </h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('departments.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>Tambah Program Keahlian
            </a>
        @endif
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('departments.index') }}" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group filter-group-search">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search"
                                   placeholder="Cari program keahlian..." value="{{ request('search') }}">
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
                        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
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
                        <th data-label="Kode">Kode</th>
                        <th data-label="Nama">Nama</th>
                        <th data-label="Sekolah">Sekolah</th>
                        <th data-label="Status">Status</th>
                        <th data-label="Kelas">Kelas</th>
                        <th data-label="Aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $dept)
                        <tr>
                            <td data-label="Kode">
                                <a href="{{ route('departments.show', $dept) }}" class="text-decoration-none fw-medium">
                                    {{ $dept->code }}
                                </a>
                            </td>
                            <td data-label="Nama">{{ $dept->name }}</td>
                            <td data-label="Sekolah">
                                @if($dept->school)
                                    {{ $dept->school->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td data-label="Status">
                                @if($dept->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td data-label="Kelas"><span class="badge badge-secondary">{{ $dept->schoolClasses()->count() }}</span></td>
                            <td data-label="Aksi">
                                <div class="table-actions">
                                    <a href="{{ route('departments.show', $dept) }}" class="btn btn-sm btn-outline-primary" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('departments.edit', $dept) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-diagram-3"></i>
                                    </div>
                                    <div class="empty-state-title">Belum ada program keahlian</div>
                                    <div class="empty-state-text">Tambahkan program keahlian untuk mengelompokkan kelas berdasarkan jurusan.</div>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('departments.create') }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus-lg me-1"></i>Tambah Program Keahlian
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($departments->hasPages())
            <div class="card-footer">
                <div class="pagination-wrapper">
                    {{ $departments->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
