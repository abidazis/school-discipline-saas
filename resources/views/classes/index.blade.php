<x-app-layout>
    <x-slot name="title">Kelas</x-slot>

    <div class="classes-page">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <div class="page-title-icon">
                    <i class="bi bi-chalkboard"></i>
                </div>
                Kelas
            </h1>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                <a href="{{ route('classes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i><span class="btn-text">Tambah</span>
                </a>
            @endif
        </div>

        <!-- Filters Card -->
        <div class="card mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('classes.index') }}" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group filter-group-search">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Cari..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="filter-group">
                            <select class="form-select" name="academic_year_id">
                                <option value="">Th. Ajaran</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <select class="form-select" name="grade_level">
                                <option value="">Tingkat</option>
                                <option value="VII" {{ request('grade_level') === 'VII' ? 'selected' : '' }}>VII</option>
                                <option value="VIII" {{ request('grade_level') === 'VIII' ? 'selected' : '' }}>VIII</option>
                                <option value="IX" {{ request('grade_level') === 'IX' ? 'selected' : '' }}>IX</option>
                                <option value="X" {{ request('grade_level') === 'X' ? 'selected' : '' }}>X</option>
                                <option value="XI" {{ request('grade_level') === 'XI' ? 'selected' : '' }}>XI</option>
                                <option value="XII" {{ request('grade_level') === 'XII' ? 'selected' : '' }}>XII</option>
                            </select>
                        </div>
                        <div class="filter-group filter-group-actions">
                            <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-filter"></i></button>
                            <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="table-scroll-wrapper">
                <table class="table table-classes">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Th. Ajaran</th>
                            <th>Tingkat</th>
                            <th>Jurusan</th>
                            <th>Siswa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schoolClasses as $class)
                            <tr>
                                <td><a href="{{ route('classes.show', $class) }}" class="text-decoration-none fw-medium">{{ $class->full_name }}</a></td>
                                <td class="text-nowrap">{{ $class->academicYear?->name ?? '-' }}</td>
                                <td><span class="grade-badge">{{ $class->grade_level }}</span></td>
                                <td>{{ $class->department?->code ?? '-' }}</td>
                                <td><span class="student-badge">{{ $class->students()->count() }}</span></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('classes.show', $class) }}" class="btn btn-outline-primary" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                            <a href="{{ route('classes.edit', $class) }}" class="btn btn-outline-secondary" title="Edit">
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
                                        <div class="empty-state-icon"><i class="bi bi-chalkboard"></i></div>
                                        <div class="empty-state-title">Belum ada kelas</div>
                                        <div class="empty-state-text">Tambahkan kelas untuk mulai.</div>
                                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                            <a href="{{ route('classes.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($schoolClasses->hasPages())
                <div class="card-footer">
                    <div class="pagination-wrapper">
                        {{ $schoolClasses->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<style>
    /* Page Container */
    .classes-page {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    /* Page Header */
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
        flex-shrink: 0;
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
        min-width: 160px;
        max-width: 280px;
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

    .table-classes {
        min-width: 600px;
        width: 100%;
        margin-bottom: 0;
        font-size: 13px;
    }

    .table-classes thead th {
        background: #f1f5f9;
        border-bottom: 2px solid #e2e8f0;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        color: #475569;
        padding: 12px 16px;
        white-space: nowrap;
        text-align: left;
    }

    .table-classes tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
        font-size: 13px;
        color: #334155;
    }

    .table-classes tbody tr:hover {
        background-color: #f8fafc;
    }

    .table-classes tbody tr:hover td {
        background-color: #f8fafc;
    }

    .table-actions {
        display: flex;
        gap: 6px;
        white-space: nowrap;
    }

    .table-actions .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }

    .table-actions .btn i {
        font-size: 14px;
    }

    .card-footer {
        padding: 14px 16px;
        border-top: 1px solid #e2e8f0;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
    }

    /* Grade level and action visibility */
    .grade-badge {
        display: inline-block;
        padding: 4px 10px;
        background: #e0e7ff;
        color: #4338ca;
        font-weight: 600;
        font-size: 12px;
        border-radius: 6px;
        text-align: center;
        min-width: 36px;
    }

    .student-badge {
        display: inline-block;
        padding: 4px 10px;
        background: #f1f5f9;
        color: #475569;
        font-weight: 500;
        font-size: 12px;
        border-radius: 6px;
        text-align: center;
        min-width: 32px;
    }

    /* Responsive - Mobile */
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

        .table-classes {
            font-size: 12px;
            min-width: 500px;
        }

        .table-classes thead th {
            font-size: 10px;
            padding: 10px 8px;
        }

        .table-classes tbody td {
            padding: 10px 10px;
            font-size: 12px;
        }

        .table-actions {
            gap: 6px;
        }

        .table-actions .btn {
            width: 30px;
            height: 30px;
        }

        .table-actions .btn i {
            font-size: 13px;
        }

        .grade-badge {
            padding: 3px 8px;
            font-size: 11px;
        }

        .student-badge {
            padding: 3px 8px;
            font-size: 11px;
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

        .table-classes {
            min-width: 420px;
        }

        .table-classes thead th {
            font-size: 9px;
            padding: 8px 6px;
        }

        .table-classes tbody td {
            padding: 10px 8px;
            font-size: 12px;
        }

        .table-actions {
            gap: 4px;
        }

        .table-actions .btn {
            width: 28px;
            height: 28px;
        }

        .table-actions .btn i {
            font-size: 12px;
        }

        .grade-badge {
            padding: 3px 8px;
            font-size: 11px;
            min-width: 32px;
        }

        .student-badge {
            padding: 3px 8px;
            font-size: 11px;
            min-width: 28px;
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
