<x-app-layout>
    <x-slot name="title">Anggota PKS - {{ $pksMember->student->full_name }}</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pks-members.index') }}">Anggota PKS</a></li>
            <li class="breadcrumb-item active">{{ $pksMember->student->full_name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 mb-1">{{ $pksMember->student->full_name }}</h1>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span class="text-muted">NIS: {{ $pksMember->student->nis }}</span>
                @if($pksMember->status === 'active')
                    <span class="badge badge-success">Aktif</span>
                @elseif($pksMember->status === 'inactive')
                    <span class="badge badge-secondary">Tidak Aktif</span>
                @elseif($pksMember->status === 'graduated')
                    <span class="badge badge-primary">Lulus</span>
                @else
                    <span class="badge badge-warning">Mengundurkan Diri</span>
                @endif
            </div>
        </div>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('pks-members.edit', $pksMember) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Data Siswa</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Nama Lengkap</dt>
                        <dd class="col-7">{{ $pksMember->student->full_name }}</dd>
                        <dt class="col-5 text-muted small">NIS</dt>
                        <dd class="col-7">{{ $pksMember->student->nis }}</dd>
                        @if($pksMember->student->nisn)
                            <dt class="col-5 text-muted small">NISN</dt>
                            <dd class="col-7">{{ $pksMember->student->nisn }}</dd>
                        @endif
                        <dt class="col-5 text-muted small">Kelas</dt>
                        <dd class="col-7">{{ $pksMember->student->schoolClass?->full_name ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Program Keahlian</dt>
                        <dd class="col-7">{{ $pksMember->student->schoolClass?->department?->name ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Jenis Kelamin</dt>
                        <dd class="col-7">{{ $pksMember->student->gender_display }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Data PKS</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Jabatan</dt>
                        <dd class="col-7">{{ $pksMember->position }}</dd>
                        <dt class="col-5 text-muted small">Status</dt>
                        <dd class="col-7">{{ $pksMember->status_display }}</dd>
                        <dt class="col-5 text-muted small">Tanggal Bergabung</dt>
                        <dd class="col-7">{{ $pksMember->joined_at->format('d F Y') }}</dd>
                        @if($pksMember->ended_at)
                            <dt class="col-5 text-muted small">Tanggal Berakhir</dt>
                            <dd class="col-7">{{ $pksMember->ended_at->format('d F Y') }}</dd>
                        @endif
                        @if($pksMember->notes)
                            <dt class="col-5 text-muted small">Catatan</dt>
                            <dd class="col-7">{{ $pksMember->notes }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('pks-members.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
        </a>
    </div>
</x-app-layout>
