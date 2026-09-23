<x-app-layout>
    <x-slot name="title">Kelas</x-slot>

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
                <i class="bi bi-plus-lg"></i>Tambah Kelas
            </a>
        @endif
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('classes.index') }}" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group filter-group-search">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Cari kelas..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="filter-group">
                        <select class="form-select" name="academic_year_id">
                            <option value="">Semua Tahun Ajaran</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <select class="form-select" name="grade_level">
                            <option value="">Semua Tingkat</option>
                            <option value="VII" {{ request('grade_level') === 'VII' ? 'selected' : '' }}>VII</option>
                            <option value="VIII" {{ request('grade_level') === 'VIII' ? 'selected' : '' }}>VIII</option>
                            <option value="IX" {{ request('grade_level') === 'IX' ? 'selected' : '' }}>IX</option>
                            <option value="X" {{ request('grade_level') === 'X' ? 'selected' : '' }}>X</option>
                            <option value="XI" {{ request('grade_level') === 'XI' ? 'selected' : '' }}>XI</option>
                            <option value="XII" {{ request('grade_level') === 'XII' ? 'selected' : '' }}>XII</option>
                        </select>
                    </div>
                    <div class="filter-group filter-group-actions">
                        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-filter"></i>Filter</button>
                        <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
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
                        <th>Kelas</th>
                        <th>Tahun Ajaran</th>
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th>Sekolah</th>
                        <th>Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schoolClasses as $class)
                        <tr>
                            <td><a href="{{ route('classes.show', $class) }}" class="text-decoration-none fw-medium">{{ $class->full_name }}</a></td>
                            <td>{{ $class->academicYear?->name ?? '-' }}</td>
                            <td>{{ $class->grade_level }}</td>
                            <td>{{ $class->department?->code ?? '-' }}</td>
                            <td>@if($class->school){{ $class->school->name }}@else<span class="text-muted">-</span>@endif</td>
                            <td><span class="badge badge-secondary">{{ $class->students()->count() }}</span></td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('classes.show', $class) }}" class="btn btn-sm btn-outline-primary" title="Lihat"><i class="bi bi-eye"></i></a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('classes.edit', $class) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="bi bi-chalkboard"></i></div>
                                    <div class="empty-state-title">Belum ada kelas</div>
                                    <div class="empty-state-text">Tambahkan kelas untuk mulai mengelola siswa.</div>
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
</x-app-layout>
