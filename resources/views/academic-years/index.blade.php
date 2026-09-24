<x-app-layout>
    <x-slot name="title">Tahun Ajaran</x-slot>

    <div class="academic-years-page">
        <div class="page-header">
            <h1 class="page-title">
                <div class="page-title-icon">
                    <i class="bi bi-calendar-range"></i>
                </div>
                Tahun Ajaran
            </h1>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                <a href="{{ route('academic-years.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i><span class="btn-text">Tambah</span>
                </a>
            @endif
        </div>

        <div class="card mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('academic-years.index') }}" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group filter-group-search">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Cari..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="filter-group">
                            <select class="form-select" name="status">
                                <option value="">Semua</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        <div class="filter-group filter-group-actions">
                            <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-filter"></i></button>
                            <a href="{{ route('academic-years.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-scroll-wrapper">
                <table class="table table-index">
                    <thead>
                        <tr>
                            <th data-label="Nama">Nama</th>
                            <th data-label="Sekolah">Sekolah</th>
                            <th data-label="Mulai">Mulai</th>
                            <th data-label="Selesai">Selesai</th>
                            <th data-label="Status">Status</th>
                            <th data-label="Aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($academicYears as $year)
                            <tr>
                                <td data-label="Nama"><a href="{{ route('academic-years.show', $year) }}" class="text-decoration-none fw-medium">{{ $year->name }}</a></td>
                                <td data-label="Sekolah" class="cell-school">@if($year->school){{ Str::limit($year->school->name, 15) }}@else<span class="text-muted">-</span>@endif</td>
                                <td data-label="Mulai" class="text-nowrap">{{ $year->start_date->format('d/m/Y') }}</td>
                                <td data-label="Selesai" class="text-nowrap">{{ $year->end_date->format('d/m/Y') }}</td>
                                <td data-label="Status">@if($year->is_active)<span class="badge badge-success">Aktif</span>@else<span class="badge badge-secondary">Nonaktif</span>@endif</td>
                                <td data-label="Aksi">
                                    <div class="table-actions">
                                        <a href="{{ route('academic-years.show', $year) }}" class="btn btn-sm btn-outline-primary" title="Lihat"><i class="bi bi-eye"></i></a>
                                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                            <a href="{{ route('academic-years.edit', $year) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="bi bi-calendar-x"></i></div>
                                        <div class="empty-state-title">Belum ada tahun ajaran</div>
                                        <div class="empty-state-text">Tambahkan tahun ajaran untuk mulai.</div>
                                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                            <a href="{{ route('academic-years.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($academicYears->hasPages())
                <div class="card-footer">
                    <div class="pagination-wrapper">
                        {{ $academicYears->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<style>
    /* Page Container */
    .academic-years-page {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    /* Page Header Responsive */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .page-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 22px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .page-title-icon {
        width: 42px;
        height: 42px;
        background: #eef2ff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4f46e5;
        font-size: 20px;
    }

    /* Filter Form */
    .filter-form {
        width: 100%;
    }

    .filter-row {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-group {
        flex-shrink: 0;
    }

    .filter-group-search {
        flex: 1;
        min-width: 180px;
        max-width: 320px;
    }

    .filter-group-actions {
        display: flex;
        gap: 6px;
    }

    .input-group {
        display: flex;
        width: 100%;
    }

    .input-group-text {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-right: none;
        color: #64748b;
        font-size: 13px;
        padding: 8px 12px;
        border-radius: 8px 0 0 8px;
    }

    .input-group .form-control {
        border-left: none;
        border-radius: 0 8px 8px 0;
        font-size: 13px;
        padding: 8px 12px;
    }

    .form-select {
        font-size: 13px;
        padding: 8px 12px;
        height: auto;
    }

    /* Table Styles */
    .table-scroll-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-index {
        min-width: 550px;
        width: 100%;
    }

    .table-index thead th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        color: #64748b;
        padding: 12px 14px;
        white-space: nowrap;
    }

    .table-index tbody td {
        padding: 12px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
    }

    .cell-school {
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .table-actions {
        display: flex;
        gap: 4px;
        white-space: nowrap;
    }

    /* Card Footer */
    .card-footer {
        padding: 14px 16px;
        border-top: 1px solid #e2e8f0;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
            margin-bottom: 16px;
        }

        .page-title {
            font-size: 18px;
        }

        .page-title-icon {
            width: 36px;
            height: 36px;
            font-size: 18px;
        }

        .page-header .btn-primary {
            width: 100%;
            justify-content: center;
        }

        .btn-text {
            display: none;
        }

        .card {
            border-radius: 10px;
            overflow: hidden;
        }

        .card-body {
            padding: 12px !important;
        }

        .filter-row {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }

        .filter-group {
            width: 100%;
        }

        .filter-group-search {
            max-width: 100%;
        }

        .filter-group-actions {
            justify-content: flex-end;
        }

        .form-select {
            font-size: 14px;
        }

        .table-scroll-wrapper {
            margin: 0 -12px;
            padding: 0 12px;
        }

        .table-index {
            font-size: 12px;
            min-width: 500px;
        }

        .table-index thead th {
            font-size: 10px;
            padding: 10px 8px;
        }

        .table-index tbody td {
            padding: 10px 8px;
            font-size: 12px;
        }

        .cell-school {
            max-width: 80px;
        }

        .btn-sm {
            padding: 5px 8px;
            font-size: 12px;
        }

        .badge {
            font-size: 10px;
            padding: 3px 6px;
        }
    }

    @media (max-width: 480px) {
        .page-header {
            margin-bottom: 12px;
        }

        .page-title {
            font-size: 16px;
            gap: 10px;
        }

        .page-title-icon {
            width: 32px;
            height: 32px;
            font-size: 16px;
            border-radius: 8px;
        }

        .card-body {
            padding: 10px !important;
        }

        .input-group-text {
            padding: 6px 10px;
            font-size: 12px;
        }

        .input-group .form-control {
            padding: 6px 10px;
            font-size: 12px;
        }

        .form-select {
            font-size: 13px;
            padding: 7px 10px;
        }

        .filter-group-actions .btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .table-scroll-wrapper {
            margin: 0 -10px;
            padding: 0 10px;
        }

        .table-index {
            min-width: 450px;
        }

        .table-index thead th {
            font-size: 9px;
            padding: 8px 6px;
        }

        .table-index tbody td {
            padding: 8px 6px;
            font-size: 11px;
        }

        .cell-school {
            max-width: 60px;
        }

        .btn-sm {
            padding: 4px 6px;
            font-size: 11px;
        }

        .badge {
            font-size: 9px;
            padding: 2px 5px;
        }

        .table-actions {
            gap: 2px;
        }

        .empty-state {
            padding: 32px 16px;
        }

        .empty-state-icon {
            width: 48px;
            height: 48px;
            font-size: 20px;
        }

        .empty-state-title {
            font-size: 14px;
        }

        .empty-state-text {
            font-size: 12px;
        }
    }
</style>
