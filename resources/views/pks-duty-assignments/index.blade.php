<x-app-layout>
    <x-slot name="title">Penugasan Piket</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Penugasan Piket</h1>
            <p class="text-muted small mb-0">Kelola penugasan anggota PKS pada jadwal piket</p>
        </div>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('pks-duty-assignments.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Penugasan
            </a>
        @endif
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pks-duty-assignments.index') }}" class="row g-3">
                <div class="col-12 col-md-3">
                    <label class="form-label small text-muted">Pencarian</label>
                    <input type="text" name="search" class="form-control" placeholder="Nama atau NIS..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Jadwal</label>
                    <select name="pks_duty_schedule_id" class="form-select">
                        <option value="">Semua Jadwal</option>
                        @foreach($schedules as $schedule)
                            <option value="{{ $schedule->id }}" {{ request('pks_duty_schedule_id') == $schedule->id ? 'selected' : '' }}>
                                {{ $schedule->schedule_date->format('d/m/Y') }} - {{ $schedule->shift?->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Lokasi</label>
                    <select name="pks_duty_location_id" class="form-select">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ request('pks_duty_location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
                        <option value="replaced" {{ request('status') == 'replaced' ? 'selected' : '' }}>Digantikan</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('pks-duty-assignments.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 px-3">Tanggal</th>
                            <th class="border-0 py-3">Shift</th>
                            <th class="border-0 py-3">Lokasi</th>
                            <th class="border-0 py-3">NIS</th>
                            <th class="border-0 py-3">Nama PKS</th>
                            <th class="border-0 py-3">Position</th>
                            <th class="border-0 py-3">Status</th>
                            <th class="border-0 py-3 px-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                            <tr>
                                <td class="px-3">{{ $assignment->schedule?->schedule_date?->format('d/m/Y') ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $assignment->schedule?->shift?->name ?? '-' }}</span>
                                </td>
                                <td>{{ $assignment->location?->name ?? '-' }}</td>
                                <td>{{ $assignment->member?->student?->nis ?? '-' }}</td>
                                <td>
                                    <div class="fw-medium">{{ $assignment->member?->student?->name ?? '-' }}</div>
                                </td>
                                <td>{{ $assignment->member?->position ?? '-' }}</td>
                                <td>
                                    @if($assignment->status === 'assigned')
                                        <span class="badge bg-success">{{ $assignment->statusDisplay }}</span>
                                    @elseif($assignment->status === 'replaced')
                                        <span class="badge bg-warning text-dark">{{ $assignment->statusDisplay }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $assignment->statusDisplay }}</span>
                                    @endif
                                </td>
                                <td class="px-3 text-end">
                                    <a href="{{ route('pks-duty-assignments.show', $assignment) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('pks-duty-assignments.edit', $assignment) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                                    Belum ada penugasan piket
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($assignments->hasPages())
            <div class="card-footer bg-white">
                {{ $assignments->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
