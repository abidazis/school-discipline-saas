<x-app-layout>
    <x-slot name="title">{{ $academicYear->name }}</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('academic-years.index') }}">Tahun Ajaran</a></li>
            <li class="breadcrumb-item active">{{ $academicYear->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 mb-1">
                <i class="bi bi-calendar3 me-2"></i>{{ $academicYear->name }}
            </h1>
            @if($academicYear->is_active)
                <span class="badge badge-success">Aktif</span>
            @else
                <span class="badge badge-secondary">Tidak Aktif</span>
            @endif
        </div>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <div class="btn-group">
                <a href="{{ route('academic-years.edit', $academicYear) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                @if(!$academicYear->schoolClasses()->exists() && !$academicYear->students()->exists())
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
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Tahun Ajaran</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted small">Sekolah</dt>
                        <dd class="mb-3">
                            @if($academicYear->school)
                                {{ $academicYear->school->name }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </dd>

                        <dt class="text-muted small">Tanggal Mulai</dt>
                        <dd class="mb-3">{{ $academicYear->start_date->format('d F Y') }}</dd>

                        <dt class="text-muted small">Tanggal Selesai</dt>
                        <dd class="mb-3">{{ $academicYear->end_date->format('d F Y') }}</dd>

                        <dt class="text-muted small">Durasi</dt>
                        <dd class="mb-0">{{ $academicYear->start_date->diffInDays($academicYear->end_date) + 1 }} hari</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Kelas ({{ $academicYear->schoolClasses->count() }})</h5>
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                        <a href="{{ route('classes.create', ['academic_year_id' => $academicYear->id]) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Tambah Kelas
                        </a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kelas</th>
                                <th>Tingkat</th>
                                <th>Jurusan</th>
                                <th>Siswa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($academicYear->schoolClasses as $class)
                                <tr>
                                    <td>
                                        <a href="{{ route('classes.show', $class) }}" class="text-decoration-none">
                                            {{ $class->full_name }}
                                        </a>
                                    </td>
                                    <td>{{ $class->grade_level }}</td>
                                    <td>{{ $class->department?->code ?? '-' }}</td>
                                    <td><span class="badge badge-secondary">{{ $class->students()->count() }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Belum ada kelas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if(!$academicYear->schoolClasses()->exists() && !$academicYear->students()->exists())
        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Tahun Ajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus <strong>{{ $academicYear->name }}</strong>?</p>
                        <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form method="POST" action="{{ route('academic-years.destroy', $academicYear) }}">
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
