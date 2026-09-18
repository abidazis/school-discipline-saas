<x-app-layout>
    <x-slot name="title">Jadwal Piket - {{ $pksDutySchedule->schedule_date->format('d/m/Y') }}</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pks-duty-schedules.index') }}">Jadwal Piket</a></li>
            <li class="breadcrumb-item active">{{ $pksDutySchedule->schedule_date->format('d/m/Y') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 mb-1">{{ $pksDutySchedule->schedule_date->format('d F Y') }}</h1>
            <span class="badge bg-primary me-1">{{ $pksDutySchedule->shift?->name ?? '-' }}</span>
            <span class="badge bg-secondary me-1">{{ $pksDutySchedule->time_range ?? '-' }}</span>
            @if($pksDutySchedule->status === 'scheduled')
                <span class="badge bg-info">Terjadwal</span>
            @elseif($pksDutySchedule->status === 'completed')
                <span class="badge bg-success">Selesai</span>
            @else
                <span class="badge bg-secondary">Dibatalkan</span>
            @endif
        </div>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('pks-duty-schedules.edit', $pksDutySchedule) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h5 class="mb-0">Detail Jadwal</h5></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Tanggal</dt>
                        <dd class="col-7">{{ $pksDutySchedule->schedule_date->format('d F Y') }}</dd>
                        <dt class="col-5 text-muted small">Shift</dt>
                        <dd class="col-7">{{ $pksDutySchedule->shift?->name ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Jam</dt>
                        <dd class="col-7">{{ $pksDutySchedule->time_range ?? '-' }}</dd>
                        <dt class="col-5 text-muted small">Jumlah Lokasi</dt>
                        <dd class="col-7">{{ $pksDutySchedule->locations->count() }}</dd>
                        @if($pksDutySchedule->notes)
                            <dt class="col-5 text-muted small">Catatan</dt>
                            <dd class="col-7">{{ $pksDutySchedule->notes }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Lokasi Piket</h5>
        </div>
        <div class="card-body">
            @if($pksDutySchedule->locations->count() > 0)
                <ul class="list-unstyled mb-0">
                    @foreach($pksDutySchedule->locations as $index => $location)
                        <li class="py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <span class="me-2 text-muted">{{ $loop->iteration }}.</span>
                            <span class="badge bg-secondary me-2">{{ $location->code }}</span>
                            {{ $location->name }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted mb-0">Tidak ada lokasi yang ditugaskan.</p>
            @endif
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Catatan:</strong> Penugasan anggota PKS akan tersedia pada tahap berikutnya.
    </div>

    <div class="mt-4">
        <a href="{{ route('pks-duty-schedules.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</x-app-layout>
