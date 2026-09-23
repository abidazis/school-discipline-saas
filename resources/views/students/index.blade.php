<x-app-layout>
    <x-slot name="title">Daftar Siswa</x-slot>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <div class="page-title-icon">
                <i class="bi bi-person-badge"></i>
            </div>
            Daftar Siswa
        </h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i>Tambah Siswa
            </a>
        @endif
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('students.index') }}" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group filter-group-search">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search"
                                   placeholder="Cari NIS, NISN, nama..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="filter-group">
                        <select class="form-select" name="academic_year_id">
                            <option value="">Semua Tahun Ajaran</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <select class="form-select" name="school_class_id">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('school_class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group filter-group-actions">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-filter"></i>Filter
                        </button>
                        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
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
                        <th data-label="NIS">NIS</th>
                        <th data-label="Nama">Nama</th>
                        <th data-label="JK">JK</th>
                        <th data-label="Kelas">Kelas</th>
                        <th data-label="Status">Status</th>
                        <th data-label="Aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td data-label="NIS" class="text-nowrap">{{ $student->nis }}</td>
                            <td data-label="Nama">
                                <a href="{{ route('students.show', $student) }}" class="text-decoration-none fw-medium">
                                    {{ $student->full_name }}
                                </a>
                                @if($student->nisn)
                                    <br><small class="text-muted">NISN: {{ $student->nisn }}</small>
                                @endif
                            </td>
                            <td data-label="JK">
                                @if($student->gender === 'male')
                                    <span class="badge badge-info">L</span>
                                @else
                                    <span class="badge badge-warning">P</span>
                                @endif
                            </td>
                            <td data-label="Kelas">
                                @if($student->schoolClass)
                                    {{ $student->schoolClass->full_name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td data-label="Status">
                                @if($student->status === 'active')
                                    <span class="badge badge-success">Aktif</span>
                                @elseif($student->status === 'graduated')
                                    <span class="badge badge-primary">Lulus</span>
                                @elseif($student->status === 'transferred')
                                    <span class="badge badge-warning">Pindah</span>
                                @else
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td data-label="Aksi">
                                <div class="table-actions">
                                    <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-primary" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                        <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
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
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <div class="empty-state-title">Belum ada siswa</div>
                                    <div class="empty-state-text">Tambahkan data siswa untuk mulai menggunakan modul ini.</div>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                        <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-person-plus me-1"></i>Tambah Siswa
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="card-footer">
                <div class="pagination-wrapper">
                    {{ $students->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
