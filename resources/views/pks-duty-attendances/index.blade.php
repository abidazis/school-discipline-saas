<x-app-layout>
    <x-slot name="title">Absensi Piket</x-slot>

    <div class="pks-page">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <div class="page-title-icon page-icon-success">
                    <i class="bi bi-person-check"></i>
                </div>
                {{ auth()->user()->isPksTeam() ? 'Absensi Piket Saya' : 'Kehadiran Piket' }}
            </h1>
            @if(auth()->user()->isPksTeam())
                <a href="{{ route('pks-duty-attendances.create') }}" class="btn btn-success">
                    <i class="bi bi-clipboard-plus me-1"></i>Catat Absensi
                </a>
            @endif
        </div>

        @if(auth()->user()->isPksTeam())
            <!-- Quick Attendance Card for PKS Team -->
            @php
                $pksMember = auth()->user()->pksMember;
                $todayAssignments = \App\Models\PksDutyAssignment::where('pks_member_id', $pksMember?->id)
                    ->where('status', \App\Models\PksDutyAssignment::STATUS_ASSIGNED)
                    ->whereHas('schedule', function($q) {
                        $q->where('schedule_date', \Carbon\Carbon::today())
                          ->whereIn('status', [\App\Models\PksDutySchedule::STATUS_SCHEDULED, \App\Models\PksDutySchedule::STATUS_COMPLETED]);
                    })
                    ->with(['schedule.shift', 'location', 'attendance'])
                    ->get();
            @endphp

            @if($todayAssignments->isNotEmpty())
                <div class="quick-attendance-card mb-4">
                    <div class="quick-attendance-header">
                        <i class="bi bi-calendar-check"></i>
                        <span>Piket Hari Ini</span>
                    </div>
                    @foreach($todayAssignments as $assignment)
                        <div class="quick-attendance-item">
                            <div class="quick-attendance-info">
                                <div class="quick-attendance-shift">
                                    <span class="shift-badge">{{ $assignment->schedule->shift->name ?? 'Shift' }}</span>
                                    <span class="shift-time">{{ $assignment->schedule->shift->start_time }} - {{ $assignment->schedule->shift->end_time }}</span>
                                </div>
                                <div class="quick-attendance-location">
                                    <i class="bi bi-geo-alt"></i> {{ $assignment->location->name ?? '-' }}
                                </div>
                            </div>
                            <div class="quick-attendance-action">
                                @if($assignment->attendance)
                                    <span class="badge {{ $assignment->attendance->status_badge_class }}">
                                        <i class="bi bi-check-circle me-1"></i>{{ $assignment->attendance->status_display }}
                                    </span>
                                @else
                                    <a href="{{ route('pks-duty-attendances.create', ['assignment_id' => $assignment->id]) }}"
                                       class="btn btn-success btn-sm">
                                        <i class="bi bi-clipboard-check me-1"></i>Isi Absensi
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        <!-- Filters Card (for non-PKS roles) -->
        @if(!auth()->user()->isPksTeam())
        <div class="card mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('pks-duty-attendances.index') }}" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group filter-group-search">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Nama/NIS..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="filter-group">
                            <select class="form-select" name="pks_duty_schedule_id">
                                <option value="">Jadwal</option>
                                @foreach($schedules as $schedule)
                                    <option value="{{ $schedule->id }}" {{ request('pks_duty_schedule_id') == $schedule->id ? 'selected' : '' }}>
                                        {{ $schedule->schedule_date->format('d/m') }} - {{ $schedule->shift?->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <select class="form-select" name="status">
                                <option value="">Status</option>
                                <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Hadir</option>
                                <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                                <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absen</option>
                                <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>Izin</option>
                            </select>
                        </div>
                        <div class="filter-group filter-group-actions">
                            <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-filter"></i></button>
                            <a href="{{ route('pks-duty-attendances.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Data Table -->
        <div class="card">
            <div class="table-scroll-wrapper">
                <table class="table table-pks">
                    <thead>
                        <tr>
                            @if(auth()->user()->isPksTeam())
                            <th>Foto</th>
                            @endif
                            <th>Tanggal</th>
                            <th>Shift</th>
                            @if(!auth()->user()->isPksTeam())
                            <th>NIS</th>
                            <th>Nama PKS</th>
                            @endif
                            <th>Status</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                @if(auth()->user()->isPksTeam())
                                <td>
                                    @if($attendance->photo_path)
                                        <img src="{{ asset('storage/' . $attendance->photo_path) }}"
                                             alt="Foto"
                                             class="attendance-photo"
                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">
                                    @else
                                        <span class="no-photo"><i class="bi bi-person"></i></span>
                                    @endif
                                </td>
                                @endif
                                <td class="text-nowrap">{{ $attendance->assignment?->schedule?->schedule_date?->format('d/m/Y') ?? '-' }}</td>
                                <td><span class="shift-badge">{{ $attendance->assignment?->schedule?->shift?->name ?? '-' }}</span></td>
                                @if(!auth()->user()->isPksTeam())
                                <td class="fw-medium">{{ $attendance->assignment?->member?->student?->nis ?? '-' }}</td>
                                <td>{{ $attendance->assignment?->member?->student?->full_name ?? '-' }}</td>
                                @endif
                                <td>
                                    <span class="status-badge {{ $attendance->status_badge_class }}">{{ $attendance->status_display }}</span>
                                </td>
                                <td><span class="time-badge">{{ $attendance->check_in_at?->format('H:i') ?? '-' }}</span></td>
                                <td><span class="time-badge">{{ $attendance->check_out_at?->format('H:i') ?? '-' }}</span></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('pks-duty-attendances.show', $attendance) }}" class="btn btn-outline-primary" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                            <a href="{{ route('pks-duty-attendances.edit', $attendance) }}" class="btn btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isPksTeam() ? 6 : 8 }}">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="bi bi-clipboard-x"></i>
                                        </div>
                                        <div class="empty-state-title">Belum ada data kehadiran</div>
                                        <div class="empty-state-text">
                                            @if(auth()->user()->isPksTeam())
                                                Data kehadiran akan muncul setelah Anda mengisi absensi piket.
                                            @else
                                                Data kehadiran akan muncul setelah ada penugasan piket.
                                            @endif
                                        </div>
                                        @if(auth()->user()->isPksTeam())
                                            <a href="{{ route('pks-duty-attendances.create') }}" class="btn btn-success mt-3">
                                                <i class="bi bi-clipboard-plus me-2"></i>Catat Absensi Sekarang
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($attendances->hasPages())
                <div class="card-footer">
                    <div class="pagination-wrapper">
                        {{ $attendances->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<style>
    .pks-page { width: 100%; max-width: 100%; box-sizing: border-box; }
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; gap: 12px; flex-wrap: wrap; }
    .page-title { display: flex; align-items: center; gap: 12px; font-size: 22px; font-weight: 700; color: #1e293b; margin: 0; }
    .page-title-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
    .page-icon-success { background: #dcfce7; color: #16a34a; }

    /* Quick Attendance Card */
    .quick-attendance-card { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac; border-radius: 12px; overflow: hidden; }
    .quick-attendance-header { background: #16a34a; color: white; padding: 12px 16px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .quick-attendance-header i { font-size: 18px; }
    .quick-attendance-item { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-bottom: 1px solid rgba(22, 163, 74, 0.1); }
    .quick-attendance-item:last-child { border-bottom: none; }
    .quick-attendance-info { display: flex; flex-direction: column; gap: 4px; }
    .quick-attendance-shift { display: flex; align-items: center; gap: 8px; }
    .quick-attendance-shift .shift-badge { background: rgba(255,255,255,0.3); color: #166534; font-weight: 600; font-size: 12px; padding: 4px 10px; border-radius: 6px; }
    .shift-time { font-size: 12px; color: #166534; font-weight: 500; }
    .quick-attendance-location { font-size: 13px; color: #166534; display: flex; align-items: center; gap: 4px; }
    .quick-attendance-action { flex-shrink: 0; }
    .quick-attendance-action .btn { padding: 8px 16px; font-weight: 500; }

    .filter-form { width: 100%; }
    .filter-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .filter-group { flex-shrink: 0; }
    .filter-group-search { flex: 1; min-width: 140px; max-width: 240px; }
    .filter-group-actions { display: flex; gap: 6px; }
    .input-group { display: flex; width: 100%; }
    .input-group-text { background: #f8fafc; border: 1px solid #e2e8f0; border-right: none; color: #64748b; font-size: 13px; padding: 8px 12px; border-radius: 8px 0 0 8px; }
    .input-group .form-control { border-left: none; border-radius: 0 8px 8px 0; font-size: 13px; padding: 8px 12px; }
    .form-select { font-size: 13px; padding: 8px 12px; height: auto; }
    .table-scroll-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-pks { min-width: 600px; width: 100%; margin-bottom: 0; font-size: 12px; }
    .table-pks thead th { background: #f1f5f9; border-bottom: 2px solid #e2e8f0; font-weight: 600; font-size: 10px; text-transform: uppercase; letter-spacing: 0.025em; color: #475569; padding: 12px 10px; white-space: nowrap; }
    .table-pks tbody td { padding: 10px 10px; vertical-align: middle; border-bottom: 1px solid #e2e8f0; font-size: 12px; color: #334155; }
    .table-pks tbody tr:hover td { background-color: #f8fafc; }
    .shift-badge { display: inline-block; padding: 3px 8px; background: #e0e7ff; color: #4338ca; font-weight: 600; font-size: 10px; border-radius: 4px; }
    .time-badge { display: inline-block; padding: 3px 8px; background: #f1f5f9; color: #475569; font-weight: 600; font-size: 11px; border-radius: 4px; min-width: 42px; text-align: center; }
    .status-badge { display: inline-block; padding: 4px 10px; font-weight: 600; font-size: 10px; border-radius: 6px; }
    .table-actions { display: flex; gap: 4px; white-space: nowrap; }
    .table-actions .btn { width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 6px; }
    .table-actions .btn i { font-size: 12px; }
    .card-footer { padding: 14px 16px; border-top: 1px solid #e2e8f0; }
    .pagination-wrapper { display: flex; justify-content: center; }

    @media (max-width: 768px) {
        .page-header { flex-direction: column; align-items: stretch; gap: 12px; margin-bottom: 16px; }
        .page-title { font-size: 18px; }
        .page-title-icon { width: 36px; height: 36px; font-size: 18px; }
        .card { border-radius: 10px; overflow: hidden; }
        .card-body { padding: 12px !important; }
        .filter-row { flex-direction: column; align-items: stretch; gap: 8px; }
        .filter-group { width: 100%; }
        .filter-group-search { max-width: 100%; }
        .filter-group-actions { justify-content: flex-end; }
        .table-scroll-wrapper { margin: 0 -12px; padding: 0 12px; }
        .table-pks { min-width: 500px; }
        .table-pks thead th { font-size: 8px; padding: 8px 6px; }
        .table-pks tbody td { padding: 8px 6px; font-size: 11px; }
        .table-actions { gap: 3px; }
        .table-actions .btn { width: 24px; height: 24px; }
        .table-actions .btn i { font-size: 10px; }
        .quick-attendance-item { flex-direction: column; align-items: flex-start; gap: 10px; }
        .quick-attendance-action { width: 100%; }
        .quick-attendance-action .btn { width: 100%; }
    }
    @media (max-width: 480px) {
        .page-header { margin-bottom: 12px; }
        .page-title { font-size: 16px; gap: 10px; }
        .page-title-icon { width: 32px; height: 32px; font-size: 16px; border-radius: 8px; }
        .card-body { padding: 10px !important; }
        .input-group-text, .input-group .form-control { padding: 6px 10px; font-size: 12px; }
        .form-select { font-size: 12px; padding: 6px 10px; }
        .filter-group-actions .btn { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; }
        .table-scroll-wrapper { margin: 0 -10px; padding: 0 10px; }
        .table-pks { min-width: 450px; }
        .table-pks thead th { font-size: 7px; padding: 6px 4px; }
        .table-pks tbody td { padding: 6px 4px; font-size: 10px; }
        .empty-state { padding: 32px 16px; }
        .empty-state-icon { width: 48px; height: 48px; font-size: 20px; }
    }
</style>
