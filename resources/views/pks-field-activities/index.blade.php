<x-app-layout>
    <x-slot name="title">Aktivitas Lapangan</x-slot>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <div class="page-title-icon">
                <i class="bi bi-map"></i>
            </div>
            Aktivitas Lapangan
        </h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
            <a href="{{ route('pks-field-activities.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Catat Aktivitas
            </a>
        @endif
    </div>

    <!-- Description -->
    <p class="text-muted mb-4">Kelola aktivitas lapangan petugas PKS</p>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pks-field-activities.index') }}" class="row g-3">
                <div class="col-12 col-md-3">
                    <label class="form-label small text-muted">Pencarian</label>
                    <input type="text" name="search" class="form-control" placeholder="Deskripsi, temuan, tindakan..." value="{{ request('search') }}">
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
                    <label class="form-label small text-muted">Jenis Aktivitas</label>
                    <select name="activity_type" class="form-select">
                        <option value="">Semua</option>
                        @foreach(\App\Models\PksFieldActivity::TYPE_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ request('activity_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('pks-field-activities.index') }}" class="btn btn-outline-secondary">
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
                        <th>Petugas</th>
                        <th>Lokasi</th>
                        <th>Jenis</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr>
                            <td class="text-nowrap">{{ $activity->activity_date->format('d/m/Y') }}</td>
                            <td>
                                <div class="fw-medium">{{ $activity->assignment?->member?->student?->full_name ?? '-' }}</div>
                                <div class="small text-muted">{{ $activity->assignment?->member?->student?->nis ?? '-' }}</div>
                            </td>
                            <td>{{ $activity->assignment?->location?->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $activity->activity_type_label }}</span>
                            </td>
                            <td>
                                <div>{{ $activity->started_at }}</div>
                                @if($activity->ended_at)
                                    <div class="small text-muted">- {{ $activity->ended_at }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $activity->status_badge_class }}">
                                    {{ $activity->status_display }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('pks-field-activities.show', $activity) }}" class="btn btn-sm btn-outline-primary" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                        <a href="{{ route('pks-field-activities.edit', $activity) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-map"></i>
                                    </div>
                                    <div class="empty-state-title">Belum ada data aktivitas lapangan</div>
                                    <div class="empty-state-text">Catat aktivitas lapangan untuk mendokumentasikan temuan PKS.</div>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                        <a href="{{ route('pks-field-activities.create') }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus-lg me-1"></i>Catat Aktivitas
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activities->hasPages())
            <div class="card-footer">
                {{ $activities->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
