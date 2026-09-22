<x-app-layout>
    <x-slot name="title">Laporan Bulanan PKS</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Laporan Bulanan PKS</h1>
            <p class="text-muted small mb-0">Ringkasan aktivitas bulanan petugas PKS</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.monthly.excel', request()->query()) }}" class="btn btn-outline-success">
                <i class="bi bi-file-excel me-1"></i>Download Excel
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
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
                    <div class="small text-muted">Total Penugasan</div>
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

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="h3 mb-1 text-success">{{ $report['summary']['total_activities'] }}</div>
                    <div class="small text-muted">Total Aktivitas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="h3 mb-1 text-danger">{{ $report['summary']['total_violations'] }}</div>
                    <div class="small text-muted">Total Pelanggaran</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="h3 mb-1 text-secondary">{{ $report['summary']['total_points'] }}</div>
                    <div class="small text-muted">Total Poin Pelanggaran</div>
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
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Rincian Harian</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 py-3 px-3">Tanggal</th>
                                <th class="border-0 py-3 text-center">Jadwal</th>
                                <th class="border-0 py-3 text-center">Petugas</th>
                                <th class="border-0 py-3 text-center">Hadir</th>
                                <th class="border-0 py-3 text-center">Terlambat</th>
                                <th class="border-0 py-3 text-center">Tidak Hadir</th>
                                <th class="border-0 py-3 text-center">Izin</th>
                                <th class="border-0 py-3 text-center">Aktivitas</th>
                                <th class="border-0 py-3 text-center">Pelanggaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report['schedules']->sortBy('schedule_date') as $schedule)
                                @php
                                    $scheduleAssignments = $schedule->assignments;
                                    $scheduleAttendances = $scheduleAssignments->pluck('attendance')->filter();
                                @endphp
                                <tr>
                                    <td class="px-3 py-2">
                                        {{ $schedule->schedule_date->format('d/m/Y') }}
                                    </td>
                                    <td class="text-center py-2">
                                        <span class="badge bg-primary">{{ $schedule->shift?->name ?? '-' }}</span>
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
                                    <td class="text-center py-2">
                                        -
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
