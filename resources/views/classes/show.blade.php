<x-app-layout>
    <x-slot name="title">{{ $schoolClass->full_name }}</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">Kelas</a></li>
            <li class="breadcrumb-item active">{{ $schoolClass->full_name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 mb-1">
                <i class="bi bi-collection me-2"></i>{{ $schoolClass->full_name }}
            </h1>
            <div class="d-flex gap-2 flex-wrap">
                @if($schoolClass->is_active)
                    <span class="badge bg-success">Aktif</span>
                @else
                    <span class="badge bg-secondary">Tidak Aktif</span>
                @endif
                <span class="badge bg-light text-dark">{{ $schoolClass->grade_level }}</span>
                @if($schoolClass->department)
                    <span class="badge bg-info">{{ $schoolClass->department->code }}</span>
                @endif
            </div>
        </div>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <div class="btn-group">
                <a href="{{ route('classes.edit', $schoolClass) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                @if(!$schoolClass->students()->exists())
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                @endif
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Informasi Kelas</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted small">Nama Lengkap</dt>
                        <dd class="mb-3">{{ $schoolClass->full_name }}</dd>

                        <dt class="text-muted small">Tingkat</dt>
                        <dd class="mb-3">{{ $schoolClass->grade_level }}</dd>

                        <dt class="text-muted small">Program Keahlian</dt>
                        <dd class="mb-3">
                            @if($schoolClass->department)
                                {{ $schoolClass->department->code }} - {{ $schoolClass->department->name }}
                            @else
                                <span class="text-muted">Umum</span>
                            @endif
                        </dd>

                        <dt class="text-muted small">Tahun Ajaran</dt>
                        <dd class="mb-3">
                            @if($schoolClass->academicYear)
                                <a href="{{ route('academic-years.show', $schoolClass->academicYear) }}">
                                    {{ $schoolClass->academicYear->name }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </dd>

                        <dt class="text-muted small">Sekolah</dt>
                        <dd class="mb-0">
                            @if($schoolClass->school)
                                {{ $schoolClass->school->name }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Siswa ({{ $schoolClass->students->count() }})</h5>
                    <a href="{{ route('students.create', ['school_class_id' => $schoolClass->id, 'academic_year_id' => $schoolClass->academic_year_id]) }}"
                       class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Siswa
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>JK</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schoolClass->students as $student)
                                <tr>
                                    <td>
                                        <a href="{{ route('students.show', $student) }}" class="text-decoration-none">
                                            {{ $student->nis }}
                                        </a>
                                    </td>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ $student->gender === 'male' ? 'L' : 'P' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $student->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Belum ada siswa.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if(!$schoolClass->students()->exists())
        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Kelas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus <strong>{{ $schoolClass->full_name }}</strong>?</p>
                        <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form method="POST" action="{{ route('classes.destroy', $schoolClass) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
