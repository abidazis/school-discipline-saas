<x-app-layout>
    <x-slot name="title">Catatan Pelanggaran</x-slot>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <div class="page-title-icon" style="background: #fef3c7; color: #d97706;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            Pelanggaran Siswa
        </h1>
        <a href="{{ route('violations.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>Catat Pelanggaran
        </a>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group filter-group-search">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Nama/NIS siswa..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="filter-group">
                        <select class="form-select" name="academic_year_id">
                            <option value="">Semua Tahun</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <select class="form-select" name="violation_type_id">
                            <option value="">Semua Jenis</option>
                            @foreach($violationTypes as $type)
                                <option value="{{ $type->id }}" {{ request('violation_type_id') == $type->id ? 'selected' : '' }}>{{ $type->code }} - {{ Str::limit($type->name, 20) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="recorded" {{ request('status') === 'recorded' ? 'selected' : '' }}>Tercatat</option>
                            <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Diverifikasi</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="filter-group filter-group-actions">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-filter"></i>Filter
                        </button>
                        <a href="{{ route('violations.index') }}" class="btn btn-outline-secondary">
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
                        <th data-label="Tanggal">Tanggal</th>
                        <th data-label="Siswa">Siswa</th>
                        <th data-label="Pelanggaran">Pelanggaran</th>
                        <th data-label="Point">Point</th>
                        <th data-label="Status">Status</th>
                        <th data-label="Aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($violations as $v)
                        <tr>
                            <td data-label="Tanggal" class="small text-nowrap">{{ $v->occurred_at->format('d/m/Y H:i') }}</td>
                            <td data-label="Siswa">
                                <a href="{{ route('students.show', $v->student) }}" class="text-decoration-none fw-medium">{{ $v->student->full_name }}</a>
                                <br><small class="text-muted">{{ $v->student->nis }}</small>
                            </td>
                            <td data-label="Pelanggaran">
                                <span class="badge badge-secondary">{{ $v->violationType->code }}</span> {{ $v->violationType->name }}
                                @if($v->location)
                                    <br><small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $v->location }}</small>
                                @endif
                            </td>
                            <td data-label="Point"><span class="badge badge-primary">{{ $v->points }} pt</span></td>
                            <td data-label="Status">
                                @if($v->status === 'recorded')
                                    <span class="badge badge-warning">Tercatat</span>
                                @elseif($v->status === 'verified')
                                    <span class="badge badge-success">Diverifikasi</span>
                                @else
                                    <span class="badge badge-secondary">Dibatalkan</span>
                                @endif
                            </td>
                            <td data-label="Aksi">
                                <div class="table-actions">
                                    <a href="{{ route('violations.show', $v) }}" class="btn btn-sm btn-outline-primary" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                    <div class="empty-state-title">Belum ada pelanggaran</div>
                                    <div class="empty-state-text">Tidak ada catatan pelanggaran untuk periode ini.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($violations->hasPages())
            <div class="card-footer">
                <div class="pagination-wrapper">
                    {{ $violations->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
