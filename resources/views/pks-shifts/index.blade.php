<x-app-layout>
    <x-slot name="title">Shift Piket</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0"><i class="bi bi-clock me-2"></i>Shift Piket</h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('pks-shifts.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Shift
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Nama shift..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                    <a href="{{ route('pks-shifts.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th>Nama Shift</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th>Status</th>
                        <th>Jumlah Jadwal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shifts as $shift)
                        <tr>
                            <td>
                                <a href="{{ route('pks-shifts.show', $shift) }}" class="text-decoration-none fw-medium">
                                    {{ $shift->name }}
                                </a>
                            </td>
                            <td>{{ $shift->start_time->format('H:i') }}</td>
                            <td>{{ $shift->end_time->format('H:i') }}</td>
                            <td>
                                @if($shift->status === 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $shift->duty_schedules_count }}</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('pks-shifts.show', $shift) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('pks-shifts.edit', $shift) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-clock fs-1 d-block mb-2"></i>
                                Belum ada shift piket.
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                    <a href="{{ route('pks-shifts.create') }}" class="text-decoration-none">Tambah Shift</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($shifts->hasPages())
            <div class="card-footer bg-white">{{ $shifts->links() }}</div>
        @endif
    </div>
</x-app-layout>
