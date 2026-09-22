<x-app-layout>
    <x-slot name="title">Kehadiran Piket</x-slot>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <div class="page-title-icon" style="background: var(--color-success-light); color: var(--color-success);">
                <i class="bi bi-person-check"></i>
            </div>
            Kehadiran Piket
        </h1>
    </div>

    <!-- Description -->
    <p class="text-muted mb-4">Kelola kehadiran petugas PKS pada jadwal piket</p>

    <!-- Filters Card -->
    <div class="card mb-4">
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
                <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('pks-duty-attendances.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
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
                        <th>Tanggal</th>
                        <th>Shift</th>
                        <th>Lokasi</th>
                        <th>NIS</th>
                        <th>Nama PKS</th>
                        <th>Status</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td class="text-nowrap">{{ $attendance->assignment?->schedule?->schedule_date?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $attendance->assignment?->schedule?->shift?->name ?? '-' }}</span>
                            </td>
                            <td>{{ $attendance->assignment?->location?->name ?? '-' }}</td>
                            <td>{{ $attendance->assignment?->member?->student?->nis ?? '-' }}</td>
                            <td class="fw-medium">{{ $attendance->assignment?->member?->student?->full_name ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $attendance->status_badge_class }}">
                                    {{ $attendance->status_display }}
                                </span>
                            </td>
                            <td>{{ $attendance->check_in_at?->format('H:i') ?? '-' }}</td>
                            <td>{{ $attendance->check_out_at?->format('H:i') ?? '-' }}</td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('pks-duty-attendances.show', $attendance) }}" class="btn btn-sm btn-outline-primary" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                        <a href="{{ route('pks-duty-attendances.edit', $attendance) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-clipboard-x"></i>
                                    </div>
                                    <div class="empty-state-title">Belum ada data kehadiran</div>
                                    <div class="empty-state-text">Data kehadiran akan muncul setelah ada penugasan piket.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
            <div class="card-footer">
                {{ $attendances->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
