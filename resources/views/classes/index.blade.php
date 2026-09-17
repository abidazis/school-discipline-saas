<x-app-layout>
    <x-slot name="title">Kelas</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0"><i class="bi bi-collection me-2"></i>Kelas</h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('classes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Kelas
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('classes.index') }}" class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Cari kelas..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select class="form-select" name="academic_year_id">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
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
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                    <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- List -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
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
                            <td>
                                <a href="{{ route('classes.show', $class) }}" class="text-decoration-none fw-medium">
                                    {{ $class->full_name }}
                                </a>
                            </td>
                            <td>{{ $class->academicYear?->name ?? '-' }}</td>
                            <td>{{ $class->grade_level }}</td>
                            <td>{{ $class->department?->code ?? '-' }}</td>
                            <td>
                                @if($class->school)
                                    <span class="text-truncate" style="max-width: 120px;">{{ $class->school->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $class->students()->count() }}</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('classes.show', $class) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('classes.edit', $class) }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-collection fs-1 d-block mb-2"></i>
                                Belum ada kelas.
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                    <a href="{{ route('classes.create') }}" class="text-decoration-none">Tambah Kelas</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($schoolClasses->hasPages())
            <div class="card-footer bg-white">
                {{ $schoolClasses->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
