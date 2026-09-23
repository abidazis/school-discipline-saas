<x-app-layout>
    <x-slot name="title">Jenis Pelanggaran</x-slot>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <div class="page-title-icon" style="background: var(--color-danger-light); color: var(--color-danger);">
                <i class="bi bi-clipboard-check"></i>
            </div>
            Jenis Pelanggaran
        </h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('violation-types.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Jenis
            </a>
        @endif
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('violation-types.index') }}" class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Kode atau nama..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <select class="form-select" name="category">
                        <option value="">Semua Kategori</option>
                        <option value="Attendance" {{ request('category') === 'Attendance' ? 'selected' : '' }}>Attendance</option>
                        <option value="Uniform" {{ request('category') === 'Uniform' ? 'selected' : '' }}>Uniform</option>
                        <option value="Behavior" {{ request('category') === 'Behavior' ? 'selected' : '' }}>Behavior</option>
                        <option value="Safety" {{ request('category') === 'Safety' ? 'selected' : '' }}>Safety</option>
                        <option value="Technology" {{ request('category') === 'Technology' ? 'selected' : '' }}>Technology</option>
                        <option value="Other" {{ request('category') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <select class="form-select" name="severity">
                        <option value="">Semua Tingkat</option>
                        <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Ringan</option>
                        <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>Berat</option>
                        <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Sangat Berat</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('violation-types.index') }}" class="btn btn-outline-secondary">
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
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Tingkat</th>
                        <th>Point</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($violationTypes as $type)
                        <tr>
                            <td>
                                <a href="{{ route('violation-types.show', $type) }}" class="text-decoration-none fw-medium">{{ $type->code }}</a>
                            </td>
                            <td>{{ $type->name }}</td>
                            <td><span class="badge badge-secondary">{{ $type->category }}</span></td>
                            <td>
                                @if($type->severity === 'low')
                                    <span class="badge badge-success">Ringan</span>
                                @elseif($type->severity === 'medium')
                                    <span class="badge badge-warning">Sedang</span>
                                @elseif($type->severity === 'high')
                                    <span class="badge badge-danger">Berat</span>
                                @else
                                    <span class="badge badge-dark">Sangat Berat</span>
                                @endif
                            </td>
                            <td><span class="badge badge-primary">{{ $type->points }} pt</span></td>
                            <td>
                                @if($type->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('violation-types.show', $type) }}" class="btn btn-sm btn-outline-primary" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('violation-types.edit', $type) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-clipboard-check"></i>
                                    </div>
                                    <div class="empty-state-title">Belum ada jenis pelanggaran</div>
                                    <div class="empty-state-text">Tambahkan jenis pelanggaran untuk mulai mencatat.</div>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('violation-types.create') }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus-lg me-1"></i>Tambah Jenis
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($violationTypes->hasPages())
            <div class="card-footer">
                {{ $violationTypes->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
