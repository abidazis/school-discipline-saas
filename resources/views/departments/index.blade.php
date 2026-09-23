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
                <i class="bi bi-plus-lg"></i>Tambah
            </a>
        @endif
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="card-header-content">
                <i class="bi bi-funnel"></i>
                <span>Filter Data</span>
            </div>
        </div>
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
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>Cari
                        </button>
                        <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- List Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-content">
                <i class="bi bi-list-ul"></i>
                <span>Daftar Program Keahlian</span>
                <span class="badge-count">{{ $departments->total() ?? 0 }}</span>
            </div>
        </div>
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
                                <a href="{{ route('departments.show', $dept) }}" class="text-decoration-none fw-medium text-primary">
                                    {{ $dept->code }}
                                </a>
                            </td>
                            <td data-label="Nama">{{ $dept->name }}</td>
                            <td data-label="Sekolah">
                                @if($dept->school)
                                    <span class="school-name">{{ $dept->school->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td data-label="Status">
                                @if($dept->is_active)
                                    <span class="badge badge-success">
                                        <i class="bi bi-check-circle"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="bi bi-x-circle"></i> Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td data-label="Kelas">
                                <span class="badge badge-info">{{ $dept->schoolClasses()->count() }} Kelas</span>
                            </td>
                            <td data-label="Aksi">
                                <div class="table-actions">
                                    <a href="{{ route('departments.show', $dept) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('departments.edit', $dept) }}" class="btn btn-sm btn-outline-warning" title="Edit">
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
                                        <a href="{{ route('departments.create') }}" class="btn btn-primary">
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
                    {{ $departments->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

<style>
    /* Card Header Content */
    .card-header-content {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        color: var(--gray-800);
    }

    .card-header-content i {
        font-size: 18px;
        color: var(--primary);
    }

    .badge-count {
        margin-left: auto;
        background: var(--gray-100);
        color: var(--gray-600);
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
    }

    /* School Name */
    .school-name {
        font-weight: 500;
    }

    /* Button Warning */
    .btn-outline-warning {
        background: transparent;
        border: 1px solid var(--warning);
        color: var(--warning);
    }

    .btn-outline-warning:hover {
        background: var(--warning);
        color: white;
    }

    /* Text Primary */
    .text-primary {
        color: var(--primary) !important;
    }

    /* Pagination Wrapper */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        padding: 16px 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .page-header .btn {
            width: 100%;
            justify-content: center;
        }

        .filter-row {
            flex-direction: column;
        }

        .filter-group,
        .filter-group-search,
        .filter-group-actions {
            width: 100%;
            min-width: 100%;
        }

        .filter-group-actions {
            flex-direction: row;
        }

        .filter-group-actions .btn {
            flex: 1;
        }
    }
</style>
