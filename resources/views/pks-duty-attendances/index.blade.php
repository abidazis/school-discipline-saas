<x-app-layout>
    <x-slot name="title">Kehadiran Piket</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Kehadiran Piket</h1>
            <p class="text-muted small mb-0">Kelola kehadiran petugas PKS pada jadwal piket</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pks-duty-attendances.index') }}" class="row g-3">
                <div class="col-12 col-md-3">
                    <label class="form-label small text-muted">Pencarian</label>
                    <input type="text" name="search" class="form-control" placeholder="Nama atau NIS..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Jadwal</label>
                    <select name="pks_duty_schedule_id" class="form-select">
                        <option value="">Semua Jadwal</option>
                        @foreach($schedules as $schedule)
                            <option value="{{ $schedule->id }}" {{ request('pks_duty_schedule_id') == $schedule->id ? 'selected' : '' }}>
                                {{ $schedule->schedule_date->format('d/m/Y') }} - {{ $schedule->shift?->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Hadir</option>
                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Tidak Hadir</option>
                        <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>Izin</option>
                    </select>
                </div>
                <div class="col-12 col-md-5 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('pks-duty-attendances.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 px-3">Tanggal</th>
                            <th class="border-0 py-3">Shift</th>
                            <th class="border-0 py-3">Lokasi</th>
                            <th class="border-0 py-3">NIS</th>
                            <th class="border-0 py-3">Nama PKS</th>
                            <th class="border-0 py-3">Status</th>
                            <th class="border-0 py-3">Check In</th>
                            <th class="border-0 py-3">Check Out</th>
                            <th class="border-0 py-3 px-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td class="px-3">{{ $attendance->assignment?->schedule?->schedule_date?->format('d/m/Y') ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $attendance->assignment?->schedule?->shift?->name ?? '-' }}</span>
                                </td>
                                <td>{{ $attendance->assignment?->location?->name ?? '-' }}</td>
                                <td>{{ $attendance->assignment?->member?->student?->nis ?? '-' }}</td>
                                <td>
                                    <div class="fw-medium">{{ $attendance->assignment?->member?->student?->full_name ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $attendance->status_badge_class }}">
                                        {{ $attendance->status_display }}
                                    </span>
                                </td>
                                <td>{{ $attendance->check_in_at?->format('H:i') ?? '-' }}</td>
                                <td>{{ $attendance->check_out_at?->format('H:i') ?? '-' }}</td>
                                <td class="px-3 text-end">
                                    <a href="{{ route('pks-duty-attendances.show', $attendance) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                        <a href="{{ route('pks-duty-attendances.edit', $attendance) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                                    Belum ada data kehadiran
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($attendances->hasPages())
            <div class="card-footer bg-white">
                {{ $attendances->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
