<x-app-layout>
    <x-slot name="title">Shift Piket</x-slot>

    <div class="pks-page">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <div class="page-title-icon page-icon-info">
                    <i class="bi bi-clock"></i>
                </div>
                Shift Piket
            </h1>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                <a href="{{ route('pks-shifts.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i><span class="btn-text">Tambah</span>
                </a>
            @endif
        </div>

        <!-- Filters Card -->
        <div class="card mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('pks-shifts.index') }}" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group filter-group-search">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Nama shift..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="filter-group">
                            <select class="form-select" name="status">
                                <option value="">Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        <div class="filter-group filter-group-actions">
                            <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-filter"></i></button>
                            <a href="{{ route('pks-shifts.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="table-scroll-wrapper">
                <table class="table table-pks">
                    <thead>
                        <tr>
                            <th>Nama Shift</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Status</th>
                            <th>Jadwal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $shift)
                            <tr>
                                <td><span class="shift-name">{{ $shift->name }}</span></td>
                                <td><span class="time-badge">{{ $shift->start_time->format('H:i') }}</span></td>
                                <td><span class="time-badge">{{ $shift->end_time->format('H:i') }}</span></td>
                                <td>
                                    @if($shift->status === 'active')
                                        <span class="status-badge status-active">Aktif</span>
                                    @else
                                        <span class="status-badge status-inactive">Nonaktif</span>
                                    @endif
                                </td>
                                <td><span class="count-badge">{{ $shift->duty_schedules_count }}</span></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('pks-shifts.show', $shift) }}" class="btn btn-outline-primary" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                            <a href="{{ route('pks-shifts.edit', $shift) }}" class="btn btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="bi bi-clock"></i>
                                        </div>
                                        <div class="empty-state-title">Belum ada shift piket</div>
                                        <div class="empty-state-text">Tambahkan shift piket untuk mengatur jadwal.</div>
                                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                            <a href="{{ route('pks-shifts.create') }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-plus-lg me-1"></i>Tambah
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($shifts->hasPages())
                <div class="card-footer">
                    <div class="pagination-wrapper">
                        {{ $shifts->links() }}
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
    .page-icon-info { background: #cffafe; color: #0891b2; }
    .filter-form { width: 100%; }
    .filter-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .filter-group { flex-shrink: 0; }
    .filter-group-search { flex: 1; min-width: 160px; max-width: 280px; }
    .filter-group-actions { display: flex; gap: 6px; }
    .input-group { display: flex; width: 100%; }
    .input-group-text { background: #f8fafc; border: 1px solid #e2e8f0; border-right: none; color: #64748b; font-size: 13px; padding: 8px 12px; border-radius: 8px 0 0 8px; }
    .input-group .form-control { border-left: none; border-radius: 0 8px 8px 0; font-size: 13px; padding: 8px 12px; }
    .form-select { font-size: 13px; padding: 8px 12px; height: auto; }
    .table-scroll-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-pks { min-width: 550px; width: 100%; margin-bottom: 0; font-size: 13px; }
    .table-pks thead th { background: #f1f5f9; border-bottom: 2px solid #e2e8f0; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.025em; color: #475569; padding: 12px 16px; white-space: nowrap; }
    .table-pks tbody td { padding: 14px 16px; vertical-align: middle; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #334155; }
    .table-pks tbody tr:hover td { background-color: #f8fafc; }
    .shift-name { font-weight: 600; color: #1e293b; }
    .time-badge { display: inline-block; padding: 5px 12px; background: #f1f5f9; color: #475569; font-weight: 600; font-size: 13px; border-radius: 6px; min-width: 56px; text-align: center; }
    .count-badge { display: inline-block; padding: 5px 12px; background: #e0e7ff; color: #4338ca; font-weight: 700; font-size: 12px; border-radius: 6px; min-width: 32px; text-align: center; }
    .status-badge { display: inline-block; padding: 4px 10px; font-weight: 600; font-size: 11px; border-radius: 6px; }
    .status-active { background: #dcfce7; color: #15803d; }
    .status-inactive { background: #f1f5f9; color: #64748b; }
    .table-actions { display: flex; gap: 6px; white-space: nowrap; }
    .table-actions .btn { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 6px; }
    .table-actions .btn i { font-size: 14px; }
    .card-footer { padding: 14px 16px; border-top: 1px solid #e2e8f0; }
    .pagination-wrapper { display: flex; justify-content: center; }
    @media (max-width: 768px) {
        .page-header { flex-direction: column; align-items: stretch; gap: 12px; margin-bottom: 16px; }
        .page-title { font-size: 18px; }
        .page-title-icon { width: 36px; height: 36px; font-size: 18px; }
        .page-header .btn-primary { width: 100%; justify-content: center; }
        .btn-text { display: none; }
        .card { border-radius: 10px; overflow: hidden; }
        .card-body { padding: 12px !important; }
        .filter-row { flex-direction: column; align-items: stretch; gap: 8px; }
        .filter-group { width: 100%; }
        .filter-group-search { max-width: 100%; }
        .filter-group-actions { justify-content: flex-end; }
        .table-scroll-wrapper { margin: 0 -12px; padding: 0 12px; }
        .table-pks { min-width: 450px; }
        .table-pks thead th { font-size: 9px; padding: 10px 8px; }
        .table-pks tbody td { padding: 10px 8px; font-size: 12px; }
        .time-badge { padding: 4px 10px; font-size: 12px; min-width: 50px; }
        .table-actions { gap: 4px; }
        .table-actions .btn { width: 28px; height: 28px; }
        .table-actions .btn i { font-size: 12px; }
    }
    @media (max-width: 480px) {
        .page-header { margin-bottom: 12px; }
        .page-title { font-size: 16px; gap: 10px; }
        .page-title-icon { width: 32px; height: 32px; font-size: 16px; border-radius: 8px; }
        .card-body { padding: 10px !important; }
        .input-group-text, .input-group .form-control { padding: 6px 10px; font-size: 12px; }
        .form-select { font-size: 13px; padding: 7px 10px; }
        .filter-group-actions .btn { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; }
        .table-scroll-wrapper { margin: 0 -10px; padding: 0 10px; }
        .table-pks { min-width: 400px; }
        .table-pks thead th { font-size: 8px; padding: 8px 6px; }
        .table-pks tbody td { padding: 8px 6px; font-size: 11px; }
        .empty-state { padding: 32px 16px; }
        .empty-state-icon { width: 48px; height: 48px; font-size: 20px; }
    }
</style>
