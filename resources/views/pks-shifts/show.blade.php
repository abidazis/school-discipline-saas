<x-app-layout>
    <x-slot name="title">{{ $pksShift->name }}</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pks-shifts.index') }}">Shift Piket</a></li>
            <li class="breadcrumb-item active">{{ $pksShift->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 mb-1">{{ $pksShift->name }}</h1>
            @if($pksShift->status === 'active')
                <span class="badge badge-success">Aktif</span>
            @else
                <span class="badge badge-secondary">Tidak Aktif</span>
            @endif
        </div>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('pks-shifts.edit', $pksShift) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Detail Shift</h5></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Nama Shift</dt>
                        <dd class="col-7">{{ $pksShift->name }}</dd>
                        <dt class="col-5 text-muted small">Jam Mulai</dt>
                        <dd class="col-7">{{ $pksShift->start_time->format('H:i') }}</dd>
                        <dt class="col-5 text-muted small">Jam Selesai</dt>
                        <dd class="col-7">{{ $pksShift->end_time->format('H:i') }}</dd>
                        <dt class="col-5 text-muted small">Durasi</dt>
                        <dd class="col-7">
                            {{ $pksShift->start_time->diffInHours($pksShift->end_time) }} jam
                        </dd>
                        <dt class="col-5 text-muted small">Jumlah Jadwal</dt>
                        <dd class="col-7">{{ $pksShift->duty_schedules_count }}</dd>
                        @if($pksShift->description)
                            <dt class="col-5 text-muted small">Deskripsi</dt>
                            <dd class="col-7">{{ $pksShift->description }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('pks-shifts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</x-app-layout>
