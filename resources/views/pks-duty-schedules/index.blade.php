<x-app-layout>
    <x-slot name="title">Jadwal Piket</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0"><i class="bi bi-calendar-check me-2"></i>Jadwal Piket</h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('pks-duty-schedules.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Jadwal
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-12 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Cari lokasi..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <select class="form-select" name="pks_shift_id">
                        <option value="">Semua Shift</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ request('pks_shift_id') == $shift->id ? 'selected' : '' }}>
                                {{ $shift->name }} ({{ $shift->time_range }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                    <a href="{{ route('pks-duty-schedules.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th>Tanggal</th>
                        <th>Shift</th>
                        <th>Jam</th>
                        <th>Jumlah Lokasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td class="small text-nowrap">
                                <a href="{{ route('pks-duty-schedules.show', $schedule) }}" class="text-decoration-none fw-medium">
                                    {{ $schedule->schedule_date->format('d/m/Y') }}
                                </a>
                            </td>
                            <td>{{ $schedule->shift->name ?? '-' }}</td>
                            <td class="small">{{ $schedule->time_range ?? '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $schedule->locations->count() }}</span></td>
                            <td>
                                @if($schedule->status === 'scheduled')
                                    <span class="badge bg-primary">Terjadwal</span>
                                @elseif($schedule->status === 'completed')
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-secondary">Dibatalkan</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('pks-duty-schedules.show', $schedule) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('pks-duty-schedules.edit', $schedule) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                Belum ada jadwal piket.
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                    <a href="{{ route('pks-duty-schedules.create') }}" class="text-decoration-none">Tambah Jadwal</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($schedules->hasPages())
            <div class="card-footer bg-white">{{ $schedules->links() }}</div>
        @endif
    </div>
</x-app-layout>
