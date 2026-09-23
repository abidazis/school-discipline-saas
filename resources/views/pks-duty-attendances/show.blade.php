<x-app-layout>
    <x-slot name="title">Detail Kehadiran</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pks-duty-attendances.index') }}">Kehadiran Piket</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Detail Kehadiran</h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
            <div>
                <a href="{{ route('pks-duty-attendances.edit', $pksDutyAttendance) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
            </div>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Kehadiran</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Jadwal Piket</dt>
                        <dd class="col-7">
                            {{ $pksDutyAttendance->assignment?->schedule?->schedule_date?->format('d F Y') ?? '-' }}
                            <br>
                            <span class="badge badge-secondary">{{ $pksDutyAttendance->assignment?->schedule?->shift?->name ?? '-' }}</span>
                        </dd>
                        <dt class="col-5 text-muted small">Lokasi Piket</dt>
                        <dd class="col-7">
                            <span class="badge badge-secondary me-1">{{ $pksDutyAttendance->assignment?->location?->code ?? '-' }}</span>
                            {{ $pksDutyAttendance->assignment?->location?->name ?? '-' }}
                        </dd>
                        <dt class="col-5 text-muted small">Status</dt>
                        <dd class="col-7">
                            <span class="badge {{ $pksDutyAttendance->status_badge_class }}">
                                {{ $pksDutyAttendance->status_display }}
                            </span>
                        </dd>
                        <dt class="col-5 text-muted small">Jam Masuk</dt>
                        <dd class="col-7">{{ $pksDutyAttendance->check_in_at?->format('H:i') ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Jam Pulang</dt>
                        <dd class="col-7">{{ $pksDutyAttendance->check_out_at?->format('H:i') ?? '-' }}</dd>
                        @if($pksDutyAttendance->notes)
                            <dt class="col-5 text-muted small">Catatan</dt>
                            <dd class="col-7">{{ $pksDutyAttendance->notes }}</dd>
                        @endif
                        <dt class="col-5 text-muted small">Dicatat Oleh</dt>
                        <dd class="col-7">{{ $pksDutyAttendance->recordedBy?->name ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Tanggal Pencatatan</dt>
                        <dd class="col-7">{{ $pksDutyAttendance->created_at?->format('d F Y H:i') ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Data Petugas</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Nama</dt>
                        <dd class="col-7 fw-medium">{{ $pksDutyAttendance->assignment?->member?->student?->full_name ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">NIS</dt>
                        <dd class="col-7">{{ $pksDutyAttendance->assignment?->member?->student?->nis ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Kelas</dt>
                        <dd class="col-7">{{ $pksDutyAttendance->assignment?->member?->student?->schoolClass?->name ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Position</dt>
                        <dd class="col-7">{{ $pksDutyAttendance->assignment?->member?->position ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Status Anggota</dt>
                        <dd class="col-7">
                            <span class="badge badge-success">{{ $pksDutyAttendance->assignment?->member?->statusDisplay ?? '-' }}</span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('pks-duty-schedules.show', $pksDutyAttendance->assignment?->pks_duty_schedule_id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Jadwal Piket
        </a>
    </div>
</x-app-layout>
