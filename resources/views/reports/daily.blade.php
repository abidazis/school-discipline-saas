<x-app-layout>
    <x-slot name="title">Laporan Harian PKS</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Laporan Harian PKS</h1>
            <p class="text-muted small mb-0">Ringkasan aktivitas harian petugas PKS</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.daily.pdf', request()->query()) }}" class="btn btn-outline-danger">
                <i class="bi bi-file-pdf me-1"></i>Download PDF
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.daily') }}" class="row g-3">
                <div class="col-12 col-md-3">
                    <label class="form-label small text-muted">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Shift</label>
                    <select name="shift_id" class="form-select">
                        <option value="">Semua Shift</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                                {{ $shift->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Lokasi</label>
                    <select name="location_id" class="form-select">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label small text-muted">Status Jadwal</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Tampilkan
                    </button>
                    <a href="{{ route('reports.daily') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="h3 mb-1 text-primary">{{ $report['summary']['total_schedules'] }}</div>
                    <div class="small text-muted">Total Jadwal</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="h3 mb-1 text-primary">{{ $report['summary']['total_assignments'] }}</div>
                    <div class="small text-muted">Total Petugas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="h3 mb-1 text-success">{{ $report['summary']['present_count'] }}</div>
                    <div class="small text-muted">Hadir</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="h3 mb-1 text-warning">{{ $report['summary']['late_count'] }}</div>
                    <div class="small text-muted">Terlambat</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="h3 mb-1 text-danger">{{ $report['summary']['absent_count'] }}</div>
                    <div class="small text-muted">Tidak Hadir</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="h3 mb-1 text-info">{{ $report['summary']['excused_count'] }}</div>
                    <div class="small text-muted">Izin</div>
                </div>
            </div>
        </div>
    </div>

    @if($report['schedules']->isEmpty() && $report['activities']->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Belum ada jadwal PKS atau aktivitas pada tanggal ini.
        </div>
    @else
        {{-- Schedules --}}
        @if($report['schedules']->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Jadwal Piket</h5>
                </div>
                <div class="card-body p-0">
                    @foreach($report['schedules'] as $schedule)
                        <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="mb-1">{{ $schedule->schedule_date->format('d F Y') }}</h6>
                                    <span class="badge bg-primary me-1">{{ $schedule->shift?->name ?? '-' }}</span>
                                    <span class="badge bg-secondary">{{ $schedule->time_range ?? '-' }}</span>
                                    @if($schedule->status === 'scheduled')
                                        <span class="badge bg-info">Terjadwal</span>
                                    @elseif($schedule->status === 'completed')
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        <span class="badge bg-secondary">Dibatalkan</span>
                                    @endif
                                </div>
                            </div>

                            @if($schedule->locations->count() > 0)
                                <div class="mb-2">
                                    <span class="small text-muted">Lokasi:</span>
                                    @foreach($schedule->locations as $location)
                                        <span class="badge bg-light text-dark me-1">{{ $location->name }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if($schedule->assignments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="border-0 py-2">Petugas</th>
                                                <th class="border-0 py-2">NIS</th>
                                                <th class="border-0 py-2">Posisi</th>
                                                <th class="border-0 py-2">Lokasi</th>
                                                <th class="border-0 py-2">Kehadiran</th>
                                                <th class="border-0 py-2">Check In</th>
                                                <th class="border-0 py-2">Check Out</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($schedule->assignments as $assignment)
                                                <tr>
                                                    <td class="py-2">
                                                        {{ $assignment->member?->student?->full_name ?? '-' }}
                                                    </td>
                                                    <td class="py-2">{{ $assignment->member?->student?->nis ?? '-' }}</td>
                                                    <td class="py-2">{{ $assignment->member?->position ?? '-' }}</td>
                                                    <td class="py-2">{{ $assignment->location?->name ?? '-' }}</td>
                                                    <td class="py-2">
                                                        @if($assignment->attendance)
                                                            <span class="badge {{ $assignment->attendance->status_badge_class }}">
                                                                {{ $assignment->attendance->status_display }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">Belum Diisi</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-2">
                                                        {{ $assignment->attendance?->check_in_at?->format('H:i') ?? '-' }}
                                                    </td>
                                                    <td class="py-2">
                                                        {{ $assignment->attendance?->check_out_at?->format('H:i') ?? '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Field Activities --}}
        @if($report['activities']->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Aktivitas Lapangan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-3 px-3">Waktu</th>
                                    <th class="border-0 py-3">Petugas</th>
                                    <th class="border-0 py-3">Lokasi</th>
                                    <th class="border-0 py-3">Jenis</th>
                                    <th class="border-0 py-3">Temuan</th>
                                    <th class="border-0 py-3">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['activities'] as $activity)
                                    <tr>
                                        <td class="px-3 py-2">
                                            {{ $activity->started_at }}
                                            @if($activity->ended_at)
                                                - {{ $activity->ended_at }}
                                            @endif
                                        </td>
                                        <td class="py-2">
                                            {{ $activity->assignment?->member?->student?->full_name ?? '-' }}
                                        </td>
                                        <td class="py-2">{{ $activity->assignment?->location?->name ?? '-' }}</td>
                                        <td class="py-2">
                                            <span class="badge bg-info">{{ $activity->activity_type_label }}</span>
                                        </td>
                                        <td class="py-2">
                                            {{ Str::limit($activity->finding, 50) ?? '-' }}
                                        </td>
                                        <td class="py-2">
                                            {{ Str::limit($activity->action_taken, 50) ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Violations --}}
        @if($report['violations']->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Pelanggaran</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-3 px-3">Waktu</th>
                                    <th class="border-0 py-3">Siswa</th>
                                    <th class="border-0 py-3">NIS</th>
                                    <th class="border-0 py-3">Jenis</th>
                                    <th class="border-0 py-3">Poin</th>
                                    <th class="border-0 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['violations'] as $violation)
                                    <tr>
                                        <td class="px-3 py-2">
                                            {{ $violation->occurred_at?->format('d/m/Y H:i') ?? '-' }}
                                        </td>
                                        <td class="py-2">
                                            {{ $violation->student?->full_name ?? '-' }}
                                        </td>
                                        <td class="py-2">{{ $violation->student?->nis ?? '-' }}</td>
                                        <td class="py-2">{{ $violation->violationType?->name ?? '-' }}</td>
                                        <td class="py-2">{{ $violation->points ?? 0 }}</td>
                                        <td class="py-2">
                                            <span class="badge bg-{{ $violation->status === 'verified' ? 'success' : 'secondary' }}">
                                                {{ $violation->status_display }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endif
</x-app-layout>
