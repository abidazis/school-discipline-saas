<x-app-layout>
    <x-slot name="title">{{ $pksDutyLocation->name }}</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pks-duty-locations.index') }}">Lokasi Piket</a></li>
            <li class="breadcrumb-item active">{{ $pksDutyLocation->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 mb-1">{{ $pksDutyLocation->name }}</h1>
            <span class="badge bg-secondary me-1">{{ $pksDutyLocation->code }}</span>
            @if($pksDutyLocation->status === 'active')
                <span class="badge bg-success">Aktif</span>
            @else
                <span class="badge bg-secondary">Tidak Aktif</span>
            @endif
        </div>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('pks-duty-locations.edit', $pksDutyLocation) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h5 class="mb-0">Detail Lokasi</h5></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Kode</dt>
                        <dd class="col-7"><span class="badge bg-secondary">{{ $pksDutyLocation->code }}</span></dd>
                        <dt class="col-5 text-muted small">Nama</dt>
                        <dd class="col-7">{{ $pksDutyLocation->name }}</dd>
                        <dt class="col-5 text-muted small">Jumlah Jadwal</dt>
                        <dd class="col-7">{{ $pksDutyLocation->duty_schedules_count }}</dd>
                        @if($pksDutyLocation->description)
                            <dt class="col-5 text-muted small">Deskripsi</dt>
                            <dd class="col-7">{{ $pksDutyLocation->description }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('pks-duty-locations.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</x-app-layout>
