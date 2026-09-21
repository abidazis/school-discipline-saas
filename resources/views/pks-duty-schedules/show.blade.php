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
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lokasi Piket</h5>
                @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                    @if($pksDutySchedule->isScheduled())
                        <a href="{{ route('pks-duty-assignments.create', ['schedule_id' => $pksDutySchedule->id]) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Tambah Petugas
                        </a>
                    @endif
                @endif
            </div>
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

    {{-- Petugas yang Ditugaskan Section --}}
    @php
        $activeAssignments = $pksDutySchedule->activeAssignments()
            ->with([
                'member.student',
                'member.student.schoolClass',
                'attendance',
                'location'
            ])
            ->get();

        $assignmentsByLocation = $activeAssignments
            ->groupBy('pks_duty_location_id');

        // Calculate attendance summary
        $totalAssignments = $activeAssignments->count();
        $presentCount = $activeAssignments->filter(fn($a) => $a->attendance && $a->attendance->isPresent())->count();
        $lateCount = $activeAssignments->filter(fn($a) => $a->attendance && $a->attendance->isLate())->count();
        $absentCount = $activeAssignments->filter(fn($a) => $a->attendance && $a->attendance->isAbsent())->count();
        $excusedCount = $activeAssignments->filter(fn($a) => $a->attendance && $a->attendance->isExcused())->count();
        $notRecordedCount = $activeAssignments->filter(fn($a) => !$a->attendance)->count();
    @endphp

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Petugas yang Ditugaskan</h5>
                @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                    @if($pksDutySchedule->isScheduled())
                        <a href="{{ route('pks-duty-assignments.create', ['schedule_id' => $pksDutySchedule->id]) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Tambah Petugas
                        </a>
                    @endif
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if($assignmentsByLocation->count() > 0)
                @foreach($pksDutySchedule->locations as $location)
                    @php
                        $locationAssignments = $assignmentsByLocation->get($location->id, collect());
                    @endphp
                    <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <h6 class="text-primary mb-3">
                            <i class="bi bi-geo-alt me-1"></i>
                            {{ $location->name }}
                            <span class="badge bg-secondary ms-1">{{ $location->code }}</span>
                        </h6>
                        @if($locationAssignments->count() > 0)
                            <div class="row g-2">
                                @foreach($locationAssignments as $assignment)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="p-3 bg-light rounded h-100">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="fw-medium">{{ $assignment->member?->student?->full_name ?? '-' }}</div>
                                                    <div class="small text-muted">
                                                        {{ $assignment->member?->student?->nis ?? '-' }}
                                                        @if($assignment->member?->student?->schoolClass)
                                                            • {{ $assignment->member?->student?->schoolClass?->name }}
                                                        @endif
                                                        • {{ $assignment->member?->position ?? '-' }}
                                                    </div>
                                                </div>
                                                <div class="btn-group btn-group-sm ms-2">
                                                    <a href="{{ route('pks-duty-assignments.show', $assignment) }}" class="btn btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                                        <a href="{{ route('pks-duty-assignments.edit', $assignment) }}" class="btn btn-outline-secondary">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($assignment->notes)
                                                <div class="small text-muted mt-1">{{ $assignment->notes }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0 small">Belum ada petugas ditugaskan</p>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people fs-1 text-muted d-block mb-3"></i>
                    <p class="text-muted mb-3">Belum ada petugas yang ditugaskan</p>
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                        @if($pksDutySchedule->isScheduled())
                            <a href="{{ route('pks-duty-assignments.create', ['schedule_id' => $pksDutySchedule->id]) }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i>Tambah Petugas
                            </a>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- KEHADIRAN PETUGAS Section --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">Kehadiran Petugas</h5>
            </div>
        </div>

        {{-- Attendance Summary --}}
        @if($totalAssignments > 0)
            <div class="card-body border-bottom bg-light">
                <div class="row g-3 text-center">
                    <div class="col-6 col-md-auto">
                        <div class="small text-muted">Total Petugas</div>
                        <div class="h5 mb-0">{{ $totalAssignments }}</div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="small text-muted">Hadir</div>
                        <div class="h5 mb-0 text-success">{{ $presentCount }}</div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="small text-muted">Terlambat</div>
                        <div class="h5 mb-0 text-warning">{{ $lateCount }}</div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="small text-muted">Izin</div>
                        <div class="h5 mb-0 text-info">{{ $excusedCount }}</div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="small text-muted">Tidak Hadir</div>
                        <div class="h5 mb-0 text-danger">{{ $absentCount }}</div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="small text-muted">Belum Diisi</div>
                        <div class="h5 mb-0 text-secondary">{{ $notRecordedCount }}</div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card-body p-0">
            @if($activeAssignments->count() > 0)
                @foreach($pksDutySchedule->locations as $location)
                    @php
                        $locationAssignments = $assignmentsByLocation->get($location->id, collect());
                    @endphp
                    @if($locationAssignments->count() > 0)
                        <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $location->name }}
                            </h6>
                            <div class="row g-2">
                                @foreach($locationAssignments as $assignment)
                                    <div class="col-12 col-md-6">
                                        <div class="border rounded p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <div class="fw-medium">{{ $assignment->member?->student?->full_name ?? '-' }}</div>
                                                    <div class="small text-muted">
                                                        {{ $assignment->member?->student?->nis ?? '-' }}
                                                        • {{ $assignment->member?->position ?? '-' }}
                                                    </div>
                                                </div>
                                                <div>
                                                    @if($assignment->attendance)
                                                        <span class="badge {{ $assignment->attendance->status_badge_class }}">
                                                            {{ $assignment->attendance->status_display }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">Belum Diisi</span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($assignment->attendance)
                                                <div class="small">
                                                    @if($assignment->attendance->check_in_at)
                                                        <div>
                                                            <i class="bi bi-box-arrow-in-right me-1 text-success"></i>
                                                            Masuk: {{ $assignment->attendance->check_in_at->format('H:i') }}
                                                        </div>
                                                    @endif
                                                    @if($assignment->attendance->check_out_at)
                                                        <div>
                                                            <i class="bi bi-box-arrow-right me-1 text-danger"></i>
                                                            Pulang: {{ $assignment->attendance->check_out_at->format('H:i') }}
                                                        </div>
                                                    @endif
                                                    @if($assignment->attendance->notes)
                                                        <div class="text-muted mt-1">
                                                            <i class="bi bi-chat-left-text me-1"></i>
                                                            {{ $assignment->attendance->notes }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="mt-2 pt-2 border-top d-flex gap-1">
                                                @if($assignment->attendance)
                                                    <a href="{{ route('pks-duty-attendances.show', $assignment->attendance) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                                        <a href="{{ route('pks-duty-attendances.edit', $assignment->attendance) }}" class="btn btn-sm btn-outline-secondary">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    @endif
                                                @else
                                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                                        <a href="{{ route('pks-duty-attendances.create', ['assignment_id' => $assignment->id]) }}" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-clipboard-plus me-1"></i>Catat Kehadiran
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>

                                            {{-- Field Activities Preview --}}
                                            @php
                                                $assignmentActivities = $assignment->fieldActivities()->completed()->orderBy('started_at', 'desc')->limit(2)->get();
                                            @endphp
                                            @if($assignmentActivities->count() > 0)
                                                <div class="mt-2 pt-2 border-top">
                                                    <div class="small text-muted mb-2">
                                                        <i class="bi bi-binoculars me-1"></i>
                                                        Aktivitas ({{ $assignment->completed_activities_count }})
                                                    </div>
                                                    @foreach($assignmentActivities as $activity)
                                                        <div class="small p-2 bg-light rounded mb-1">
                                                            <span class="badge bg-info me-1">{{ $activity->activity_type_label }}</span>
                                                            {{ $activity->started_at }}
                                                            @if($activity->ended_at)
                                                                - {{ $activity->ended_at }}
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                    @if($assignment->completed_activities_count > 2)
                                                        <a href="{{ route('pks-field-activities.index', ['assignment_id' => $assignment->id]) }}" class="small text-primary">
                                                            Lihat semua ({{ $assignment->completed_activities_count }})
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- Catat Aktivitas Button --}}
                                            @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin() || auth()->user()->isOperator())
                                                <div class="mt-2">
                                                    <a href="{{ route('pks-field-activities.create', ['assignment_id' => $assignment->id]) }}" class="btn btn-sm btn-outline-success w-100">
                                                        <i class="bi bi-binoculars me-1"></i>Catat Aktivitas
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x fs-1 text-muted d-block mb-3"></i>
                    <p class="text-muted mb-0">Belum ada petugas yang ditugaskan</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Show all assignments including replaced/cancelled --}}
    @php
        $allAssignments = $pksDutySchedule->assignments()
            ->with(['member.student', 'location'])
            ->where('status', '!=', 'assigned')
            ->get();
    @endphp

    @if($allAssignments->count() > 0)
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Riwayat Penugasan Sebelumnya</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 py-3 px-3">Nama</th>
                                <th class="border-0 py-3">Lokasi</th>
                                <th class="border-0 py-3">Status</th>
                                <th class="border-0 py-3 px-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allAssignments as $assignment)
                                <tr>
                                    <td class="px-3">
                                        <div class="fw-medium">{{ $assignment->member?->student?->full_name ?? '-' }}</div>
                                        <div class="small text-muted">{{ $assignment->member?->student?->nis ?? '-' }}</div>
                                    </td>
                                    <td>{{ $assignment->location?->name ?? '-' }}</td>
                                    <td>
                                        @if($assignment->status === 'replaced')
                                            <span class="badge bg-warning text-dark">{{ $assignment->statusDisplay }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $assignment->statusDisplay }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 text-end">
                                        <a href="{{ route('pks-duty-assignments.show', $assignment) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('pks-duty-schedules.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</x-app-layout>
