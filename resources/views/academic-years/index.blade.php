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
            <a href="{{ route('academic-years.create') }}" class="btn-app btn-app-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Tahun Ajaran
            </a>
        @endif
    </div>

    <div class="app-card mb-4">
        <div class="app-card-body">
            <form method="GET" action="{{ route('academic-years.index') }}" class="row g-3">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Cari tahun ajaran..." value="{{ request('search') }}">
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
                    <button type="submit" class="btn-app btn-app-outline-primary"><i class="bi bi-filter me-1"></i>Filter</button>
                    <a href="{{ route('academic-years.index') }}" class="btn-app btn-app-outline"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="app-card">
        <div class="table-responsive">
            <table class="app-table">
                <thead>
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
                            <td><a href="{{ route('academic-years.show', $year) }}" class="text-decoration-none fw-medium">{{ $year->name }}</a></td>
                            <td>@if($year->school)<span class="text-truncate d-inline-block" style="max-width:150px">{{ $year->school->name }}</span>@else<span class="text-muted">-</span>@endif</td>
                            <td class="text-nowrap">{{ $year->start_date->format('d M Y') }}</td>
                            <td class="text-nowrap">{{ $year->end_date->format('d M Y') }}</td>
                            <td>@if($year->is_active)<span class="badge-app badge-success">Aktif</span>@else<span class="badge-app badge-secondary">Tidak Aktif</span>@endif</td>
                            <td><span class="badge-app badge-secondary">{{ $year->schoolClasses()->count() }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('academic-years.show', $year) }}" class="btn-app btn-app-sm btn-app-outline" title="Lihat"><i class="bi bi-eye"></i></a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('academic-years.edit', $year) }}" class="btn-app btn-app-sm btn-app-outline" title="Edit"><i class="bi bi-pencil"></i></a>
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
                                        <a href="{{ route('academic-years.create') }}" class="btn-app btn-app-sm btn-app-primary"><i class="bi bi-plus-lg me-1"></i>Tambah</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($academicYears->hasPages())
            <div class="app-card-footer">{{ $academicYears->links() }}</div>
        @endif
    </div>
</x-app-layout>
