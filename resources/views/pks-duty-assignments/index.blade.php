<x-app-layout>
    <x-slot name="title">Penugasan Piket</x-slot>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <div class="page-title-icon">
                <i class="bi bi-calendar-check"></i>
            </div>
            Penugasan Piket
        </h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('pks-duty-assignments.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Penugasan
            </a>
        @endif
    </div>

    <!-- Description -->
    <p class="text-muted mb-4">Kelola penugasan anggota PKS pada jadwal piket</p>

    <!-- Filters Card -->
    <div class="card mb-4">
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
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('pks-duty-assignments.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- List Card -->
    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Shift</th>
                        <th>Lokasi</th>
                        <th>NIS</th>
                        <th>Nama PKS</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr>
                            <td class="text-nowrap">{{ $assignment->schedule?->schedule_date?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                <span class="badge badge-secondary">{{ $assignment->schedule?->shift?->name ?? '-' }}</span>
                            </td>
                            <td>{{ $assignment->location?->name ?? '-' }}</td>
                            <td>{{ $assignment->member?->student?->nis ?? '-' }}</td>
                            <td class="fw-medium">{{ $assignment->member?->student?->name ?? '-' }}</td>
                            <td>{{ $assignment->member?->position ?? '-' }}</td>
                            <td>
                                @if($assignment->status === 'assigned')
                                    <span class="badge badge-success">{{ $assignment->statusDisplay }}</span>
                                @elseif($assignment->status === 'replaced')
                                    <span class="badge badge-warning">{{ $assignment->statusDisplay }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ $assignment->statusDisplay }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('pks-duty-assignments.show', $assignment) }}" class="btn btn-sm btn-outline-primary" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('pks-duty-assignments.edit', $assignment) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-clipboard-x"></i>
                                    </div>
                                    <div class="empty-state-title">Belum ada penugasan piket</div>
                                    <div class="empty-state-text">Tambahkan penugasan untuk menugaskan anggota PKS.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($assignments->hasPages())
            <div class="card-footer">
                {{ $assignments->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
