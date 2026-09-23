<x-app-layout>
    <x-slot name="title">Laporan Bulanan PKS</x-slot>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <div class="page-title-icon" style="background: var(--color-info-light); color: var(--color-info);">
                <i class="bi bi-calendar-event"></i>
            </div>
            Laporan Bulanan PKS
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.monthly.excel', request()->query()) }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Download Excel
            </a>
        </div>
    </div>

    <!-- Description -->
    <p class="text-muted mb-4">Ringkasan aktivitas bulanan petugas PKS</p>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.monthly') }}" class="row g-3">
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">Bulan</label>
                    <select name="month" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">Tahun</label>
                    <select name="year" class="form-select">
                        @for($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Shift</label>
                    <select name="shift_id" class="form-select">
                        <option value="">Semua</option>
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
                        <option value="">Semua</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Tampilkan
                    </button>
                    <a href="{{ route('reports.monthly') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <h5 class="mb-3">{{ $report['period'] }}</h5>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="bi bi-calendar-week"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $report['summary']['total_schedules'] }}</div>
                    <div class="stat-label">Total Jadwal</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="bi bi-clipboard-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $report['summary']['total_assignments'] }}</div>
                    <div class="stat-label">Total Penugasan</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $report['summary']['present_count'] }}</div>
                    <div class="stat-label">Hadir</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="bi bi-alarm"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $report['summary']['late_count'] }}</div>
                    <div class="stat-label">Terlambat</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-icon danger">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $report['summary']['absent_count'] }}</div>
                    <div class="stat-label">Tidak Hadir</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $report['summary']['excused_count'] }}</div>
                    <div class="stat-label">Izin</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="bi bi-list-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $report['summary']['total_activities'] }}</div>
                    <div class="stat-label">Total Aktivitas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="stat-icon danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $report['summary']['total_violations'] }}</div>
                    <div class="stat-label">Total Pelanggaran</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="bi bi-star"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $report['summary']['total_points'] }}</div>
                    <div class="stat-label">Total Poin Pelanggaran</div>
                </div>
            </div>
        </div>
    </div>

    @if($report['schedules']->isEmpty() && $report['activities']->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Tidak ada data PKS untuk periode ini.
        </div>
    @else
        {{-- Daily Breakdown --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="bi bi-calendar-week text-primary"></i>
                    Rincian Harian
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 text-center">Jadwal</th>
                            <th class="py-3 text-center">Petugas</th>
                            <th class="py-3 text-center">Hadir</th>
                            <th class="py-3 text-center">Terlambat</th>
                            <th class="py-3 text-center">Tidak Hadir</th>
                            <th class="py-3 text-center">Izin</th>
                            <th class="py-3 text-center">Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report['schedules']->sortBy('schedule_date') as $schedule)
                            @php
                                $scheduleAssignments = $schedule->assignments;
                                $scheduleAttendances = $scheduleAssignments->pluck('attendance')->filter();
                            @endphp
                            <tr>
                                <td class="px-3 py-2 text-nowrap">
                                    {{ $schedule->schedule_date->format('d/m/Y') }}
                                </td>
                                <td class="text-center py-2">
                                    <span class="badge badge-primary">{{ $schedule->shift?->name ?? '-' }}</span>
                                </td>
                                <td class="text-center py-2">
                                    {{ $scheduleAssignments->count() }}
                                </td>
                                <td class="text-center py-2">
                                    <span class="text-success">{{ $scheduleAttendances->where('status', 'present')->count() }}</span>
                                </td>
                                <td class="text-center py-2">
                                    <span class="text-warning">{{ $scheduleAttendances->where('status', 'late')->count() }}</span>
                                </td>
                                <td class="text-center py-2">
                                    <span class="text-danger">{{ $scheduleAttendances->where('status', 'absent')->count() }}</span>
                                </td>
                                <td class="text-center py-2">
                                    <span class="text-info">{{ $scheduleAttendances->where('status', 'excused')->count() }}</span>
                                </td>
                                <td class="text-center py-2">
                                    {{ $scheduleAssignments->pluck('fieldActivities')->flatten()->where('status', 'completed')->count() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-app-layout>
