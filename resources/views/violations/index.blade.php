<x-app-layout>
    <x-slot name="title">Catatan Pelanggaran</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Pelanggaran Siswa</h1>
        <a href="{{ route('violations.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Catat Pelanggaran</a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-12 col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Nama/NIS siswa..." value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-2">
                    <select class="form-select" name="academic_year_id">
                        <option value="">Semua Tahun</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <select class="form-select" name="violation_type_id">
                        <option value="">Semua Jenis</option>
                        @foreach($violationTypes as $type)
                            <option value="{{ $type->id }}" {{ request('violation_type_id') == $type->id ? 'selected' : '' }}>{{ $type->code }} - {{ Str::limit($type->name, 20) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="recorded" {{ request('status') === 'recorded' ? 'selected' : '' }}>Tercatat</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Diverifikasi</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                    <a href="{{ route('violations.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Pelanggaran</th>
                        <th>Point</th>
                        <th>Petugas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($violations as $v)
                        <tr>
                            <td class="small text-nowrap">{{ $v->occurred_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('students.show', $v->student) }}" class="text-decoration-none">{{ $v->student->full_name }}</a>
                                <br><small class="text-muted">{{ $v->student->nis }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $v->violationType->code }}</span> {{ $v->violationType->name }}
                                @if($v->location)<br><small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $v->location }}</small>@endif
                            </td>
                            <td><span class="badge bg-primary">{{ $v->points }} pt</span></td>
                            <td class="small">{{ $v->officer->name }}</td>
                            <td>
                                @if($v->status === 'recorded')
                                    <span class="badge bg-warning">Tercatat</span>
                                @elseif($v->status === 'verified')
                                    <span class="badge bg-success">Diverifikasi</span>
                                @else
                                    <span class="badge bg-secondary">Dibatalkan</span>
                                @endif
                            </td>
                            <td><a href="{{ route('violations.show', $v) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-check-circle fs-1 d-block mb-2"></i>Belum ada pelanggaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($violations->hasPages())<div class="card-footer bg-white">{{ $violations->links() }}</div>@endif
    </div>
</x-app-layout>