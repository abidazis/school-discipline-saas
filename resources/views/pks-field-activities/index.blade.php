<x-app-layout>
    <x-slot name="title">Aktivitas Lapangan</x-slot>

    <div class="pks-page">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <div class="page-title-icon page-icon-info">
                    <i class="bi bi-map"></i>
                </div>
                Aktivitas Lapangan
            </h1>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                <a href="{{ route('pks-field-activities.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i><span class="btn-text">Catat</span>
                </a>
            @endif
        </div>

        <!-- Filters Card -->
        <div class="card mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('pks-field-activities.index') }}" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group filter-group-search">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Cari..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="filter-group">
                            <select class="form-select" name="activity_type">
                                <option value="">Jenis</option>
                                @foreach(\App\Models\PksFieldActivity::TYPE_LABELS as $value => $label)
                                    <option value="{{ $value }}" {{ request('activity_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <select class="form-select" name="status">
                                <option value="">Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Batal</option>
                            </select>
                        </div>
                        <div class="filter-group filter-group-actions">
                            <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-filter"></i></button>
                            <a href="{{ route('pks-field-activities.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
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
                            <th>Tanggal</th>
                            <th>Petugas</th>
                            <th>Lokasi</th>
                            <th>Jenis</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            <tr>
                                <td class="text-nowrap">{{ $activity->activity_date->format('d/m/Y') }}</td>
                                <td>
                                    <div class="fw-medium">{{ Str::limit($activity->assignment?->member?->student?->full_name ?? '-', 15) }}</div>
                                    <div class="small text-muted">{{ $activity->assignment?->member?->student?->nis ?? '-' }}</div>
                                </td>
                                <td>{{ Str::limit($activity->assignment?->location?->name ?? '-', 15) }}</td>
                                <td><span class="type-badge">{{ $activity->activity_type_label }}</span></td>
                                <td><span class="time-text">{{ $activity->started_at }}</span></td>
                                <td>
                                    <span class="status-badge {{ $activity->status_badge_class }}">{{ $activity->status_display }}</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('pks-field-activities.show', $activity) }}" class="btn btn-outline-primary" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                            <a href="{{ route('pks-field-activities.edit', $activity) }}" class="btn btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="bi bi-map"></i>
                                        </div>
                                        <div class="empty-state-title">Belum ada data aktivitas</div>
                                        <div class="empty-state-text">Catat aktivitas lapangan untuk dokumentasi PKS.</div>
                                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                            <a href="{{ route('pks-field-activities.create') }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-plus-lg me-1"></i>Catat
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($activities->hasPages())
                <div class="card-footer">
                    <div class="pagination-wrapper">
                        {{ $activities->withQueryString()->links() }}
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
    .filter-group-search { flex: 1; min-width: 140px; max-width: 240px; }
    .filter-group-actions { display: flex; gap: 6px; }
    .input-group { display: flex; width: 100%; }
    .input-group-text { background: #f8fafc; border: 1px solid #e2e8f0; border-right: none; color: #64748b; font-size: 13px; padding: 8px 12px; border-radius: 8px 0 0 8px; }
    .input-group .form-control { border-left: none; border-radius: 0 8px 8px 0; font-size: 13px; padding: 8px 12px; }
    .form-select { font-size: 13px; padding: 8px 12px; height: auto; }
    .table-scroll-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-pks { min-width: 750px; width: 100%; margin-bottom: 0; font-size: 12px; }
    .table-pks thead th { background: #f1f5f9; border-bottom: 2px solid #e2e8f0; font-weight: 600; font-size: 10px; text-transform: uppercase; letter-spacing: 0.025em; color: #475569; padding: 12px 10px; white-space: nowrap; }
    .table-pks tbody td { padding: 10px 10px; vertical-align: middle; border-bottom: 1px solid #e2e8f0; font-size: 12px; color: #334155; }
    .table-pks tbody tr:hover td { background-color: #f8fafc; }
    .type-badge { display: inline-block; padding: 3px 8px; background: #e0e7ff; color: #4338ca; font-weight: 600; font-size: 10px; border-radius: 4px; }
    .time-text { font-size: 11px; color: #64748b; }
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
        .page-header .btn-primary { width: 100%; justify-content: center; }
        .btn-text { display: none; }
        .card { border-radius: 10px; overflow: hidden; }
        .card-body { padding: 12px !important; }
        .filter-row { flex-direction: column; align-items: stretch; gap: 8px; }
        .filter-group { width: 100%; }
        .filter-group-search { max-width: 100%; }
        .filter-group-actions { justify-content: flex-end; }
        .table-scroll-wrapper { margin: 0 -12px; padding: 0 12px; }
        .table-pks { min-width: 650px; }
        .table-pks thead th { font-size: 8px; padding: 8px 6px; }
        .table-pks tbody td { padding: 8px 6px; font-size: 11px; }
        .table-actions { gap: 3px; }
        .table-actions .btn { width: 24px; height: 24px; }
        .table-actions .btn i { font-size: 10px; }
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
        .table-pks { min-width: 550px; }
        .table-pks thead th { font-size: 7px; padding: 6px 4px; }
        .table-pks tbody td { padding: 6px 4px; font-size: 10px; }
        .empty-state { padding: 32px 16px; }
        .empty-state-icon { width: 48px; height: 48px; font-size: 20px; }
    }
</style>
