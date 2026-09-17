<x-app-layout>
    <x-slot name="title">Jenis Pelanggaran</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Jenis Pelanggaran</h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('violation-types.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Jenis
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('violation-types.index') }}" class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Kode atau nama..." value="{{ request('search') }}">
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
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                    <a href="{{ route('violation-types.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- List -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
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
                            <td><span class="badge bg-secondary">{{ $type->category }}</span></td>
                            <td><span class="badge bg-{{ $type->severity === 'low' ? 'success' : ($type->severity === 'medium' ? 'warning' : ($type->severity === 'high' ? 'danger' : 'dark')) }}">{{ ucfirst($type->severity) }}</span></td>
                            <td><span class="badge bg-primary">{{ $type->points }} pt</span></td>
                            <td>
                                @if($type->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('violation-types.show', $type) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('violation-types.edit', $type) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-exclamation-triangle fs-1 d-block mb-2"></i>
                                Belum ada jenis pelanggaran.
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                    <a href="{{ route('violation-types.create') }}" class="text-decoration-none">Tambah Jenis</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($violationTypes->hasPages())
            <div class="card-footer bg-white">{{ $violationTypes->links() }}</div>
        @endif
    </div>
</x-app-layout>