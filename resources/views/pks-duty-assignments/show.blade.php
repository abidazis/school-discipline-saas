<x-app-layout>
    <x-slot name="title">Detail Penugasan Piket</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pks-duty-assignments.index') }}">Penugasan Piket</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Detail Penugasan Piket</h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <div>
                <a href="{{ route('pks-duty-assignments.edit', $pksDutyAssignment) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
            </div>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Informasi Penugasan</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Jadwal Piket</dt>
                        <dd class="col-7">
                            {{ $pksDutyAssignment->schedule?->schedule_date?->format('d F Y') ?? '-' }}
                            <br>
                            <span class="badge bg-secondary">{{ $pksDutyAssignment->schedule?->shift?->name ?? '-' }}</span>
                        </dd>
                        <dt class="col-5 text-muted small">Lokasi Piket</dt>
                        <dd class="col-7">
                            <span class="badge bg-secondary me-1">{{ $pksDutyAssignment->location?->code ?? '-' }}</span>
                            {{ $pksDutyAssignment->location?->name ?? '-' }}
                        </dd>
                        <dt class="col-5 text-muted small">Status</dt>
                        <dd class="col-7">
                            @if($pksDutyAssignment->status === 'assigned')
                                <span class="badge bg-success">{{ $pksDutyAssignment->statusDisplay }}</span>
                            @elseif($pksDutyAssignment->status === 'replaced')
                                <span class="badge bg-warning text-dark">{{ $pksDutyAssignment->statusDisplay }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $pksDutyAssignment->statusDisplay }}</span>
                            @endif
                        </dd>
                        <dt class="col-5 text-muted small">Tanggal Penugasan</dt>
                        <dd class="col-7">{{ $pksDutyAssignment->assigned_at?->format('d F Y H:i') ?? '-' }}</dd>
                        @if($pksDutyAssignment->notes)
                            <dt class="col-5 text-muted small">Catatan</dt>
                            <dd class="col-7">{{ $pksDutyAssignment->notes }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Data Anggota PKS</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Nama</dt>
                        <dd class="col-7 fw-medium">{{ $pksDutyAssignment->member?->student?->name ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">NIS</dt>
                        <dd class="col-7">{{ $pksDutyAssignment->member?->student?->nis ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Kelas</dt>
                        <dd class="col-7">{{ $pksDutyAssignment->member?->student?->schoolClass?->name ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Position</dt>
                        <dd class="col-7">{{ $pksDutyAssignment->member?->position ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Status Anggota</dt>
                        <dd class="col-7">
                            @if($pksDutyAssignment->member?->isActive())
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">{{ $pksDutyAssignment->member?->statusDisplay ?? '-' }}</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('pks-duty-schedules.show', $pksDutyAssignment->pks_duty_schedule_id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Jadwal Piket
        </a>
    </div>
</x-app-layout>
