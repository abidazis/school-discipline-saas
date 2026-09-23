<x-app-layout>
    <x-slot name="title">Tahun Ajaran</x-slot>

    <div class="page-header">
        <h1 class="page-title">
            <div class="page-title-icon">
                <i class="bi bi-calendar-range"></i>
            </div>
            Tahun Ajaran
        </h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('academic-years.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>Tambah Tahun Ajaran
            </a>
        @endif
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('academic-years.index') }}" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group filter-group-search">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Cari tahun ajaran..." value="{{ request('search') }}">
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
                        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-filter"></i>Filter</button>
                        <a href="{{ route('academic-years.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th data-label="Nama">Nama</th>
                        <th data-label="Sekolah">Sekolah</th>
                        <th data-label="Tanggal Mulai">Tanggal Mulai</th>
                        <th data-label="Tanggal Selesai">Tanggal Selesai</th>
                        <th data-label="Status">Status</th>
                        <th data-label="Kelas">Kelas</th>
                        <th data-label="Aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($academicYears as $year)
                        <tr>
                            <td data-label="Nama"><a href="{{ route('academic-years.show', $year) }}" class="text-decoration-none fw-medium">{{ $year->name }}</a></td>
                            <td data-label="Sekolah">@if($year->school){{ $year->school->name }}@else<span class="text-muted">-</span>@endif</td>
                            <td data-label="Tanggal Mulai" class="text-nowrap">{{ $year->start_date->format('d M Y') }}</td>
                            <td data-label="Tanggal Selesai" class="text-nowrap">{{ $year->end_date->format('d M Y') }}</td>
                            <td data-label="Status">@if($year->is_active)<span class="badge badge-success">Aktif</span>@else<span class="badge badge-secondary">Tidak Aktif</span>@endif</td>
                            <td data-label="Kelas"><span class="badge badge-secondary">{{ $year->schoolClasses()->count() }}</span></td>
                            <td data-label="Aksi">
                                <div class="table-actions">
                                    <a href="{{ route('academic-years.show', $year) }}" class="btn btn-sm btn-outline-primary" title="Lihat"><i class="bi bi-eye"></i></a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('academic-years.edit', $year) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="bi bi-calendar-x"></i></div>
                                    <div class="empty-state-title">Belum ada tahun ajaran</div>
                                    <div class="empty-state-text">Tambahkan tahun ajaran untuk mulai mengelola kelas.</div>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('academic-years.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($academicYears->hasPages())
            <div class="card-footer">
                <div class="pagination-wrapper">
                    {{ $academicYears->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
