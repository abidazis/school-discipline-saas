<x-app-layout>
    <x-slot name="title">Aktivitas Lapangan</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Aktivitas Lapangan</h1>
            <p class="text-muted small mb-0">Kelola aktivitas lapangan petugas PKS</p>
        </div>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
            <a href="{{ route('pks-field-activities.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Catat Aktivitas
            </a>
        @endif
    </div>

    <div class="card border-0 shadow-sm mb-4">
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
                <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('pks-field-activities.index') }}" class="btn btn-outline-secondary">
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
                            <th class="border-0 py-3">Petugas</th>
                            <th class="border-0 py-3">Lokasi</th>
                            <th class="border-0 py-3">Jenis</th>
                            <th class="border-0 py-3">Waktu</th>
                            <th class="border-0 py-3">Status</th>
                            <th class="border-0 py-3 px-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            <tr>
                                <td class="px-3">{{ $activity->activity_date->format('d/m/Y') }}</td>
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
                                <td class="px-3 text-end">
                                    <a href="{{ route('pks-field-activities.show', $activity) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                        <a href="{{ route('pks-field-activities.edit', $activity) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                                    Belum ada data aktivitas lapangan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($activities->hasPages())
            <div class="card-footer bg-white">
                {{ $activities->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
