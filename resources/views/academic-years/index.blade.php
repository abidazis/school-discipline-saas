<x-app-layout>
    <x-slot name="title">Tahun Ajaran</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0"><i class="bi bi-calendar3 me-2"></i>Tahun Ajaran</h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('academic-years.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Tahun Ajaran
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('academic-years.index') }}" class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Cari tahun ajaran..." value="{{ request('search') }}">
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
                    <a href="{{ route('academic-years.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th>Nama</th>
                        <th>Sekolah</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($academicYears as $year)
                        <tr>
                            <td>
                                <a href="{{ route('academic-years.show', $year) }}" class="text-decoration-none fw-medium">
                                    {{ $year->name }}
                                </a>
                            </td>
                            <td>
                                @if($year->school)
                                    <span class="text-truncate" style="max-width: 150px;">{{ $year->school->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $year->start_date->format('d M Y') }}</td>
                            <td>{{ $year->end_date->format('d M Y') }}</td>
                            <td>
                                @if($year->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $year->schoolClasses()->count() }}</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('academic-years.show', $year) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('academic-years.edit', $year) }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                Belum ada tahun ajaran.
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                    <a href="{{ route('academic-years.create') }}" class="text-decoration-none">Tambah Tahun Ajaran</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($academicYears->hasPages())
            <div class="card-footer bg-white">
                {{ $academicYears->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
