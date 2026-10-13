<x-app-layout>
    <x-slot name="title">Riwayat Pelanggaran</x-slot>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <div class="page-title-icon" style="background: #fef3c7; color: #d97706;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            Riwayat Pelanggaran
        </h1>
        <a href="{{ route('violations.create') }}" class="btn btn-warning">
            <i class="bi bi-plus-lg"></i>Catat Baru
        </a>
    </div>

    <!-- Simple Search (Mobile Friendly) -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="d-flex gap-2">
                <div class="flex-grow-1">
                    <input type="text"
                           class="form-control"
                           name="search"
                           placeholder="Cari nama/NIS..."
                           value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('violations.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- List Card - Mobile Friendly Card View -->
    <div class="card">
        @forelse($violations as $v)
            <a href="{{ route('violations.show', $v) }}"
               class="text-decoration-none text-dark border-bottom p-3 d-block">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-0">
                            <i class="bi bi-person text-warning me-1"></i>{{ $v->student->full_name }}
                        </h6>
                        <small class="text-muted">NIS: {{ $v->student->nis }}</small>
                    </div>
                    <span class="badge {{ $v->status === 'recorded' ? 'bg-warning text-dark' : ($v->status === 'verified' ? 'bg-success' : 'bg-secondary') }}">
                        @if($v->status === 'recorded')
                            Tercatat
                        @elseif($v->status === 'verified')
                            Diverifikasi
                        @else
                            Dibatalkan
                        @endif
                    </span>
                </div>
                <div class="row g-2 small">
                    <div class="col-7">
                        <div class="text-truncate">
                            <span class="badge bg-secondary mb-1">{{ $v->violationType->code }}</span>
                            {{ $v->violationType->name }}
                        </div>
                        @if($v->location)
                            <div class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i>{{ $v->location }}
                            </div>
                        @endif
                    </div>
                    <div class="col-5 text-end">
                        <div class="text-danger fw-bold">{{ $v->points }} poin</div>
                        <div class="text-muted">
                            <i class="bi bi-clock me-1"></i>{{ $v->occurred_at->format('d/m H:i') }}
                        </div>
                    </div>
                </div>
                @if($v->evidences->isNotEmpty())
                    <div class="mt-2">
                        <span class="badge bg-info">
                            <i class="bi bi-camera me-1"></i>{{ $v->evidences->count() }} foto
                        </span>
                    </div>
                @endif
            </a>
        @empty
            <div class="card-body text-center py-5">
                <div class="text-muted mb-3">
                    <i class="bi bi-check-circle" style="font-size: 3rem;"></i>
                </div>
                <h5 class="text-muted">Belum Ada Pelanggaran</h5>
                <p class="text-muted small">Tidak ada catatan pelanggaran untuk pencarian ini.</p>
                <a href="{{ route('violations.create') }}" class="btn btn-warning mt-2">
                    <i class="bi bi-plus-lg me-2"></i>Catat Pelanggaran
                </a>
            </div>
        @endforelse

        @if($violations->hasPages())
            <div class="card-footer">
                {{ $violations->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <style>
        @media (min-width: 768px) {
            .card a.border-bottom:hover {
                background-color: #f8f9fa;
            }
        }
    </style>
</x-app-layout>
