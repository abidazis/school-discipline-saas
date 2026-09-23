<x-app-layout>
    <x-slot name="title">Detail Aktivitas Lapangan</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pks-field-activities.index') }}">Aktivitas Lapangan</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Detail Aktivitas Lapangan</h1>
        <div class="d-flex gap-2">
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                @if(!$pksFieldActivity->isCancelled())
                    <form action="{{ route('pks-field-activities.cancel', $pksFieldActivity) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Yakin ingin membatalkan aktivitas ini?')">
                            <i class="bi bi-x-circle me-1"></i>Batalkan
                        </button>
                    </form>
                @endif
                <a href="{{ route('pks-field-activities.edit', $pksFieldActivity) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
            @endif
        </div>
    </div>

    <div class="row g-4">
        {{-- Assignment Information --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Penugasan</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Petugas</dt>
                        <dd class="col-7 fw-medium">{{ $pksFieldActivity->assignment?->member?->student?->full_name ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">NIS</dt>
                        <dd class="col-7">{{ $pksFieldActivity->assignment?->member?->student?->nis ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Kelas</dt>
                        <dd class="col-7">{{ $pksFieldActivity->assignment?->member?->student?->schoolClass?->name ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Position</dt>
                        <dd class="col-7">{{ $pksFieldActivity->assignment?->member?->position ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Jadwal Piket</dt>
                        <dd class="col-7">
                            {{ $pksFieldActivity->assignment?->schedule?->schedule_date?->format('d F Y') ?? '-' }}
                            <br>
                            <span class="badge badge-secondary">{{ $pksFieldActivity->assignment?->schedule?->shift?->name ?? '-' }}</span>
                        </dd>
                        <dt class="col-5 text-muted small">Lokasi Piket</dt>
                        <dd class="col-7">
                            <span class="badge badge-secondary me-1">{{ $pksFieldActivity->assignment?->location?->code ?? '-' }}</span>
                            {{ $pksFieldActivity->assignment?->location?->name ?? '-' }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Activity Information --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Detail Aktivitas</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Jenis Aktivitas</dt>
                        <dd class="col-7">
                            <span class="badge badge-info">{{ $pksFieldActivity->activity_type_label }}</span>
                        </dd>
                        <dt class="col-5 text-muted small">Tanggal</dt>
                        <dd class="col-7">{{ $pksFieldActivity->activity_date?->format('d F Y') ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Jam Mulai</dt>
                        <dd class="col-7">{{ $pksFieldActivity->started_at ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Jam Selesai</dt>
                        <dd class="col-7">{{ $pksFieldActivity->ended_at ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Status</dt>
                        <dd class="col-7">
                            <span class="badge {{ $pksFieldActivity->status_badge_class }}">
                                {{ $pksFieldActivity->status_display }}
                            </span>
                        </dd>
                        <dt class="col-5 text-muted small">Dicatat Oleh</dt>
                        <dd class="col-7">{{ $pksFieldActivity->recordedBy?->name ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Tanggal Pencatatan</dt>
                        <dd class="col-7">{{ $pksFieldActivity->created_at?->format('d/m/Y H:i') ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Content --}}
    <div class="row g-4 mt-0">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Catatan Lapangan</h5>
                </div>
                <div class="card-body">
                    @if($pksFieldActivity->description)
                        <div class="mb-4">
                            <h6 class="text-muted small mb-2">DESKRIPSI AKTIVITAS</h6>
                            <p class="mb-0">{{ $pksFieldActivity->description }}</p>
                        </div>
                    @endif

                    @if($pksFieldActivity->finding)
                        <div class="mb-4">
                            <h6 class="text-muted small mb-2">TEMUAN</h6>
                            <p class="mb-0">{{ $pksFieldActivity->finding }}</p>
                        </div>
                    @endif

                    @if($pksFieldActivity->action_taken)
                        <div class="mb-4">
                            <h6 class="text-muted small mb-2">TINDAKAN YANG DILAKUKAN</h6>
                            <p class="mb-0">{{ $pksFieldActivity->action_taken }}</p>
                        </div>
                    @endif

                    @if($pksFieldActivity->notes)
                        <div class="alert alert-secondary mb-0">
                            <h6 class="text-muted small mb-2">CATATAN</h6>
                            <p class="mb-0">{{ $pksFieldActivity->notes }}</p>
                        </div>
                    @endif

                    @if(!$pksFieldActivity->description && !$pksFieldActivity->finding && !$pksFieldActivity->action_taken && !$pksFieldActivity->notes)
                        <p class="text-muted mb-0">Tidak ada catatan lapangan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Violation Link --}}
    @if($pksFieldActivity->violation)
        <div class="row g-4 mt-0">
            <div class="col-12">
                <div class="card border-danger">
                    <div class="card-header">
                        <h5 class="mb-0 text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Pelanggaran Terkait
                        </h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-3 text-muted small">ID Pelanggaran</dt>
                            <dd class="col-9">
                                <a href="{{ route('violations.show', $pksFieldActivity->violation) }}">
                                    #{{ $pksFieldActivity->violation->id }}
                                </a>
                            </dd>
                            <dt class="col-3 text-muted small">Siswa</dt>
                            <dd class="col-9">{{ $pksFieldActivity->violation->student?->full_name ?? '-' }}</dd>
                            <dt class="col-3 text-muted small">Jenis</dt>
                            <dd class="col-9">{{ $pksFieldActivity->violation->violationType?->name ?? '-' }}</dd>
                            <dt class="col-3 text-muted small">Poin</dt>
                            <dd class="col-9">{{ $pksFieldActivity->violation->points ?? '-' }}</dd>
                            <dt class="col-3 text-muted small">Status</dt>
                            <dd class="col-9">
                                <span class="badge {{ $pksFieldActivity->violation->status === 'verified' ? 'badge badge-success' : 'badge badge-secondary' }}">
                                    {{ $pksFieldActivity->violation->status_display }}
                                </span>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('pks-field-activities.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</x-app-layout>
