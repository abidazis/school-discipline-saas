<x-app-layout>
    <x-slot name="title">Program Keahlian</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0"><i class="bi bi-book me-2"></i>Program Keahlian</h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('departments.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Program Keahlian
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('departments.index') }}" class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Cari program keahlian..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                    <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th>Sekolah</th>
                        <th>Status</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $dept)
                        <tr>
                            <td>
                                <a href="{{ route('departments.show', $dept) }}" class="text-decoration-none fw-medium">
                                    {{ $dept->code }}
                                </a>
                            </td>
                            <td>{{ $dept->name }}</td>
                            <td>
                                @if($dept->school)
                                    <span class="text-truncate" style="max-width: 150px;">{{ $dept->school->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($dept->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $dept->schoolClasses()->count() }}</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('departments.show', $dept) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('departments.edit', $dept) }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-book-x fs-1 d-block mb-2"></i>
                                Belum ada program keahlian.
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                    <a href="{{ route('departments.create') }}" class="text-decoration-none">Tambah Program Keahlian</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($departments->hasPages())
            <div class="card-footer bg-white">
                {{ $departments->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
