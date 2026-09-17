<x-app-layout>
    <x-slot name="title">Daftar Siswa</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0"><i class="bi bi-mortarboard me-2"></i>Daftar Siswa</h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Siswa
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('students.index') }}" class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Cari NIS, NISN, nama..." value="{{ request('search') }}">
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
                    <select class="form-select" name="school_class_id">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('school_class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th class="text-nowrap">NIS</th>
                        <th>Nama</th>
                        <th>JK</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td class="text-nowrap">{{ $student->nis }}</td>
                            <td>
                                <a href="{{ route('students.show', $student) }}" class="text-decoration-none">
                                    {{ $student->full_name }}
                                </a>
                                @if($student->nisn)
                                    <br><small class="text-muted">NISN: {{ $student->nisn }}</small>
                                @endif
                            </td>
                            <td>{{ $student->gender === 'male' ? 'L' : 'P' }}</td>
                            <td>
                                @if($student->schoolClass)
                                    {{ $student->schoolClass->full_name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($student->status === 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @elseif($student->status === 'graduated')
                                    <span class="badge bg-primary">Lulus</span>
                                @elseif($student->status === 'transferred')
                                    <span class="badge bg-warning">Pindah</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('students.show', $student) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                        <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-mortarboard fs-1 d-block mb-2"></i>
                                Belum ada siswa.
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                    <a href="{{ route('students.create') }}" class="text-decoration-none">Tambah Siswa</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="card-footer bg-white">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
