<x-app-layout>
    <x-slot name="title">Pelanggaran {{ $violation->student->nis }}</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('violations.index') }}">Pelanggaran</a></li>
            <li class="breadcrumb-item active">{{ $violation->student->nis }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header with Status Badge -->
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h4 mb-1">
                <i class="bi bi-person me-2 text-warning"></i>{{ $violation->student->full_name }}
            </h1>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span class="text-muted">
                    <i class="bi bi-123 me-1"></i>NIS: {{ $violation->student->nis }}
                </span>
                <span class="badge {{ $violation->status === 'recorded' ? 'bg-warning text-dark' : ($violation->status === 'verified' ? 'bg-success' : 'bg-secondary') }}">
                    @if($violation->status === 'recorded')
                        <i class="bi bi-clock me-1"></i>Tercatat
                    @elseif($violation->status === 'verified')
                        <i class="bi bi-check-circle me-1"></i>Diverifikasi
                    @else
                        <i class="bi bi-x-circle me-1"></i>Dibatalkan
                    @endif
                </span>
                @if($violation->evidences->isNotEmpty())
                    <span class="badge bg-info">
                        <i class="bi bi-camera me-1"></i>{{ $violation->evidences->count() }} Foto
                    </span>
                @endif
            </div>
        </div>

        <!-- Action Buttons - Only show for authorized roles -->
        @canany(['update', 'verify', 'cancel'], $violation)
            <div class="btn-group">
                @can('verify', $violation)
                    @if($violation->canVerify())
                        <form method="POST" action="{{ route('violations.verify', $violation) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i>Verifikasi
                            </button>
                        </form>
                    @endif
                @endcan

                @can('cancel', $violation)
                    @if($violation->canCancel())
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="bi bi-x-lg me-1"></i>Batalkan
                        </button>
                    @endif
                @endcan

                @can('update', $violation)
                    @if($violation->canEdit())
                        <a href="{{ route('violations.edit', $violation) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                    @endif
                @endcan
            </div>
        @endcanany
    </div>

    <div class="row g-4">
        <!-- Detail Card -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clipboard me-2"></i>Detail Pelanggaran</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Jenis</dt>
                        <dd class="col-7">
                            <span class="badge bg-secondary">{{ $violation->violationType->code }}</span>
                            {{ $violation->violationType->name }}
                        </dd>

                        <dt class="col-5 text-muted small">Kategori</dt>
                        <dd class="col-7">{{ $violation->violationType->category }}</dd>

                        <dt class="col-5 text-muted small">Tingkat</dt>
                        <dd class="col-7">
                            <span class="badge bg-{{ $violation->violationType->severity === 'ringan' ? 'success' : ($violation->violationType->severity === 'sedang' ? 'warning text-dark' : 'danger') }}">
                                {{ ucfirst($violation->violationType->severity) }}
                            </span>
                        </dd>

                        <dt class="col-5 text-muted small">Point</dt>
                        <dd class="col-7">
                            <strong class="text-danger fs-5">{{ $violation->points }} point</strong>
                        </dd>

                        <dt class="col-5 text-muted small">Waktu Kejadian</dt>
                        <dd class="col-7">{{ $violation->occurred_at->format('d F Y, H:i') }} WIB</dd>

                        @if($violation->location)
                            <dt class="col-5 text-muted small">Lokasi</dt>
                            <dd class="col-7">
                                <i class="bi bi-geo-alt text-danger me-1"></i>{{ $violation->location }}
                            </dd>
                        @endif

                        @if($violation->description)
                            <dt class="col-5 text-muted small">Keterangan</dt>
                            <dd class="col-7">{{ $violation->description }}</dd>
                        @endif

                        <dt class="col-5 text-muted small">Petugas</dt>
                        <dd class="col-7">{{ $violation->officer->name }}</dd>

                        <dt class="col-5 text-muted small">Dicatat</dt>
                        <dd class="col-7">{{ $violation->created_at->format('d/m/Y H:i') }}</dd>

                        @if($violation->isCancelled() && $violation->cancelled_reason)
                            <dt class="col-5 text-muted small">Alasan Batal</dt>
                            <dd class="col-7 text-danger">{{ $violation->cancelled_reason }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Student Card -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Informasi Siswa</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Nama</dt>
                        <dd class="col-7 fw-bold">{{ $violation->student->full_name }}</dd>

                        <dt class="col-5 text-muted small">NIS</dt>
                        <dd class="col-7">{{ $violation->student->nis }}</dd>

                        <dt class="col-5 text-muted small">Kelas</dt>
                        <dd class="col-7">{{ $violation->schoolClass->full_name ?? '-' }}</dd>

                        <dt class="col-5 text-muted small">Tahun Ajaran</dt>
                        <dd class="col-7">{{ $violation->academicYear->name }}</dd>

                        <dt class="col-5 text-muted small">Total Point Aktif</dt>
                        <dd class="col-7">
                            <span class="badge bg-danger fs-6">
                                {{ $violation->student->active_points }} point
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Evidence Gallery -->
    @if($violation->evidences->isNotEmpty())
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-images me-2"></i>Bukti Foto ({{ $violation->evidences->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($violation->evidences as $evidence)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('evidences.show', $evidence) }}" target="_blank" class="d-block position-relative">
                                <img src="{{ route('evidences.show', $evidence) }}"
                                     class="img-thumbnail w-100"
                                     style="height: 150px; object-fit: cover;"
                                     alt="Bukti {{ $loop->iteration }}">
                                <span class="position-absolute bottom-0 start-0 badge bg-dark opacity-75">
                                    <i class="bi bi-zoom-in"></i>
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Cancel Modal - Only for authorized roles -->
    @can('cancel', $violation)
        @if($violation->canCancel())
            <div class="modal fade" id="cancelModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-exclamation-triangle text-danger me-2"></i>Batalkan Pelanggaran
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('violations.cancel', $violation) }}">
                            @csrf
                            <div class="modal-body">
                                <p class="text-muted">Yakin batalkan pelanggaran ini? Point akan dikeluarkan dari total siswa.</p>
                                <div class="mb-3">
                                    <label for="cancelReason" class="form-label">Alasan Pembatalan</label>
                                    <textarea class="form-control"
                                              name="reason"
                                              id="cancelReason"
                                              rows="2"
                                              placeholder="Contoh: Kesalahan input, siswa tidak hadir hari itu, dll"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-x-circle me-1"></i>Ya, Batalkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    <style>
        @media (max-width: 576px) {
            .card-body dl dt,
            .card-body dl dd {
                display: block;
                width: 100%;
            }
            .card-body dl dt {
                margin-bottom: 0.25rem;
            }
            .card-body dl dd {
                margin-bottom: 1rem;
            }
        }
    </style>
</x-app-layout>
