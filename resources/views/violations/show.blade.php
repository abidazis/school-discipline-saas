<x-app-layout>
    <x-slot name="title>Pelanggaran {{ $violation->student->nis }}</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('violations.index') }}">Pelanggaran</a></li>
            <li class="breadcrumb-item active">{{ $violation->student->nis }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 mb-1">{{ $violation->student->full_name }}</h1>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span class="text-muted">NIS: {{ $violation->student->nis }}</span>
                <span class="badge bg-{{ $violation->status === 'recorded' ? 'warning' : ($violation->status === 'verified' ? 'success' : 'secondary') }}">
                    {{ $violation->status === 'recorded' ? 'Tercatat' : ($violation->status === 'verified' ? 'Diverifikasi' : 'Dibatalkan') }}
                </span>
            </div>
        </div>
        <div class="btn-group">
            @if($violation->canVerify())
                <form method="POST" action="{{ route('violations.verify', $violation) }}">@csrf<button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Verifikasi</button></form>
            @endif
            @if($violation->canCancel())
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal"><i class="bi bi-x-lg me-1"></i>Batalkan</button>
            @endif
            <a href="{{ route('violations.edit', $violation) }}" class="btn btn-outline-primary {{ $violation->canEdit() ? '' : 'disabled' }}"><i class="bi bi-pencil me-1"></i>Edit</a>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h5 class="mb-0">Detail Pelanggaran</h5></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Jenis</dt><dd class="col-7">{{ $violation->violationType->code }} - {{ $violation->violationType->name }}</dd>
                        <dt class="col-5 text-muted small">Kategori</dt><dd class="col-7">{{ $violation->violationType->category }}</dd>
                        <dt class="col-5 text-muted small">Tingkat</dt><dd class="col-7">{{ ucfirst($violation->violationType->severity) }}</dd>
                        <dt class="col-5 text-muted small">Point</dt><dd class="col-7"><strong>{{ $violation->points }} point</strong></dd>
                        <dt class="col-5 text-muted small">Tanggal Kejadian</dt><dd class="col-7">{{ $violation->occurred_at->format('d F Y H:i') }}</dd>
                        @if($violation->location)<dt class="col-5 text-muted small">Lokasi</dt><dd class="col-7">{{ $violation->location }}</dd>@endif
                        @if($violation->description)<dt class="col-5 text-muted small">Keterangan</dt><dd class="col-7">{{ $violation->description }}</dd>@endif
                        <dt class="col-5 text-muted small">Petugas</dt><dd class="col-7">{{ $violation->officer->name }}</dd>
                        <dt class="col-5 text-muted small">Dicatat</dt><dd class="col-7">{{ $violation->created_at->format('d/m/Y H:i') }}</dd>
                        @if($violation->isCancelled() && $violation->cancelled_reason)
                            <dt class="col-5 text-muted small">Alasan Batal</dt><dd class="col-7">{{ $violation->cancelled_reason }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Siswa</h5>
                    <a href="{{ route('students.show', $violation->student) }}" class="btn btn-sm btn-outline-primary">Profil</a>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Nama</dt><dd class="col-7">{{ $violation->student->full_name }}</dd>
                        <dt class="col-5 text-muted small">NIS</dt><dd class="col-7">{{ $violation->student->nis }}</dd>
                        <dt class="col-5 text-muted small">Kelas</dt><dd class="col-7">{{ $violation->schoolClass->full_name ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Tahun Ajaran</dt><dd class="col-7">{{ $violation->academicYear->name }}</dd>
                        <dt class="col-5 text-muted small">Total Point Aktif</dt><dd class="col-7"><strong class="text-danger">{{ $violation->student->active_points }} point</strong></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    @if($violation->evidences->isNotEmpty())
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Bukti Foto ({{ $violation->evidences->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($violation->evidences as $evidence)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="position-relative">
                                <img src="{{ route('evidences.show', $evidence) }}" class="img-thumbnail" alt="Bukti">
                                <form method="POST" action="{{ route('violations.evidences.destroy', $evidence) }}" class="position-absolute top-0 end-0 m-1">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus bukti ini?')"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if($violation->canCancel())
        <div class="modal fade" id="cancelModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Batalkan Pelanggaran</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <form method="POST" action="{{ route('violations.cancel', $violation) }}">
                        @csrf
                        <div class="modal-body">
                            <p>Yakin batalkan pelanggaran ini? Point akan dikeluarkan dari total siswa.</p>
                            <textarea class="form-control" name="reason" rows="2" placeholder="Alasan pembatalan (opsional)"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>