<x-app-layout>
    <x-slot name="title">{{ $violationType->name }}</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('violation-types.index') }}">Jenis Pelanggaran</a></li>
            <li class="breadcrumb-item active">{{ $violationType->code }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 mb-1">{{ $violationType->name }} <small class="text-muted">({{ $violationType->code }})</small></h1>
            <div class="d-flex gap-2 flex-wrap mt-2">
                <span class="badge bg-{{ $violationType->is_active ? 'success' : 'secondary' }}">
                    {{ $violationType->is_active ? 'Aktif' : 'Tidak Aktif' }}
                </span>
                <span class="badge bg-secondary">{{ $violationType->category }}</span>
                <span class="badge bg-{{ $violationType->severity === 'low' ? 'success' : ($violationType->severity === 'medium' ? 'warning' : 'danger') }}">
                    {{ ucfirst($violationType->severity) }}
                </span>
            </div>
        </div>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <div class="btn-group">
                <a href="{{ route('violation-types.edit', $violationType) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
                @if(!$violationType->isUsed())
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="bi bi-trash me-1"></i>Hapus</button>
                @endif
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h5 class="mb-0">Detail</h5></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted small">Kode</dt><dd class="col-7">{{ $violationType->code }}</dd>
                        <dt class="col-5 text-muted small">Nama</dt><dd class="col-7">{{ $violationType->name }}</dd>
                        <dt class="col-5 text-muted small">Kategori</dt><dd class="col-7">{{ $violationType->category }}</dd>
                        <dt class="col-5 text-muted small">Tingkat</dt><dd class="col-7">{{ ucfirst($violationType->severity) }}</dd>
                        <dt class="col-5 text-muted small">Point</dt><dd class="col-7">{{ $violationType->points }} point</dd>
                        @if($violationType->description)
                            <dt class="col-5 text-muted small">Deskripsi</dt><dd class="col-7">{{ $violationType->description }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h5 class="mb-0">Statistik</h5></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-6 text-muted small">Total Pelanggaran</dt><dd class="col-6"><span class="badge bg-secondary">{{ $violationType->violations()->count() }}</span></dd>
                        <dt class="col-6 text-muted small">Aktif</dt><dd class="col-6"><span class="badge bg-success">{{ $violationType->violations()->where('status', '!=', 'cancelled')->count() }}</span></dd>
                        <dt class="col-6 text-muted small">Diverifikasi</dt><dd class="col-6"><span class="badge bg-primary">{{ $violationType->violations()->where('status', 'verified')->count() }}</span></dd>
                        <dt class="col-6 text-muted small">Dibatalkan</dt><dd class="col-6"><span class="badge bg-secondary">{{ $violationType->violations()->where('status', 'cancelled')->count() }}</span></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    @if(!$violationType->isUsed())
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Hapus Jenis Pelanggaran</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <p>Hapus <strong>{{ $violationType->name }}</strong>?</p>
                        <p class="text-muted small">Tindakan tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form method="POST" action="{{ route('violation-types.destroy', $violationType) }}">@csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>