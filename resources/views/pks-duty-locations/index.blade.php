<x-app-layout>
    <x-slot name="title">Lokasi Piket</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0"><i class="bi bi-geo-alt me-2"></i>Lokasi Piket</h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('pks-duty-locations.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Lokasi
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Nama atau kode..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                    <a href="{{ route('pks-duty-locations.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- List -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Lokasi</th>
                        <th>Status</th>
                        <th>Jumlah Jadwal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $location)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $location->code }}</span>
                            </td>
                            <td>
                                <a href="{{ route('pks-duty-locations.show', $location) }}" class="text-decoration-none fw-medium">
                                    {{ $location->name }}
                                </a>
                            </td>
                            <td>
                                @if($location->status === 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $location->duty_schedules_count }}</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('pks-duty-locations.show', $location) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('pks-duty-locations.edit', $location) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-geo-alt fs-1 d-block mb-2"></i>
                                Belum ada lokasi piket.
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                    <a href="{{ route('pks-duty-locations.create') }}" class="text-decoration-none">Tambah Lokasi</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($locations->hasPages())
            <div class="card-footer bg-white">{{ $locations->links() }}</div>
        @endif
    </div>
</x-app-layout>
