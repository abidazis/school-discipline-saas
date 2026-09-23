<x-app-layout>
    <x-slot name="title">{{ $student->full_name }}</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Siswa</a></li>
            <li class="breadcrumb-item active">{{ $student->full_name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 mb-1">
                <i class="bi bi-mortarboard me-2"></i>{{ $student->full_name }}
            </h1>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span class="text-muted">NIS: {{ $student->nis }}</span>
                @if($student->nisn)
                    <span class="text-muted">| NISN: {{ $student->nisn }}</span>
                @endif
                @if($student->status === 'active')
                    <span class="badge badge-success">Aktif</span>
                @elseif($student->status === 'graduated')
                    <span class="badge badge-primary">Lulus</span>
                @elseif($student->status === 'transferred')
                    <span class="badge badge-warning">Pindah</span>
                @else
                    <span class="badge badge-secondary">Tidak Aktif</span>
                @endif
            </div>
        </div>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
            <div class="btn-group">
                <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
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
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Identitas Siswa</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">NIS</dt>
                        <dd class="col-7">{{ $student->nis }}</dd>

                        <dt class="col-5 text-muted">NISN</dt>
                        <dd class="col-7">{{ $student->nisn ?? '-' }}</dd>

                        <dt class="col-5 text-muted">Nama Lengkap</dt>
                        <dd class="col-7">{{ $student->full_name }}</dd>

                        <dt class="col-5 text-muted">Jenis Kelamin</dt>
                        <dd class="col-7">{{ $student->gender_display }}</dd>

                        <dt class="col-5 text-muted">Tempat Lahir</dt>
                        <dd class="col-7">{{ $student->birth_place ?? '-' }}</dd>

                        <dt class="col-5 text-muted">Tanggal Lahir</dt>
                        <dd class="col-7">{{ $student->birth_date ? $student->birth_date->format('d F Y') : '-' }}</dd>

                        <dt class="col-5 text-muted">Alamat</dt>
                        <dd class="col-7">{{ $student->address ?? '-' }}</dd>

                        <dt class="col-5 text-muted">Telepon</dt>
                        <dd class="col-7">{{ $student->phone ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Akademik</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Sekolah</dt>
                        <dd class="col-7">
                            @if($student->school)
                                {{ $student->school->name }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </dd>

                        <dt class="col-5 text-muted">Tahun Ajaran</dt>
                        <dd class="col-7">
                            @if($student->academicYear)
                                {{ $student->academicYear->name }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </dd>

                        <dt class="col-5 text-muted">Kelas</dt>
                        <dd class="col-7">
                            @if($student->schoolClass)
                                <a href="{{ route('classes.show', $student->schoolClass) }}">
                                    {{ $student->schoolClass->full_name }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </dd>

                        <dt class="col-5 text-muted">Program Keahlian</dt>
                        <dd class="col-7">
                            @if($student->schoolClass?->department)
                                {{ $student->schoolClass->department->code }} - {{ $student->schoolClass->department->name }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </dd>

                        <dt class="col-5 text-muted">Status</dt>
                        <dd class="col-7">
                            @if($student->status === 'active')
                                <span class="badge badge-success">Aktif</span>
                            @elseif($student->status === 'graduated')
                                <span class="badge badge-primary">Lulus</span>
                            @elseif($student->status === 'transferred')
                                <span class="badge badge-warning">Pindah</span>
                            @else
                                <span class="badge badge-secondary">Tidak Aktif</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus <strong>{{ $student->full_name }}</strong>?</p>
                    <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form method="POST" action="{{ route('students.destroy', $student) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
