<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <!-- Welcome Banner for PKS Team -->
    @if(auth()->user()->isPksTeam())
        <div class="welcome-banner pks-welcome">
            <div class="welcome-banner-icon" style="background: #fef3c7; color: #d97706;">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="welcome-banner-content">
                <h2 class="welcome-banner-title">Selamat Datang, {{ auth()->user()->name }}!</h2>
                <p class="welcome-banner-text">
                    Anda login sebagai <strong>Tim PKS</strong> {{ auth()->user()->school ? 'di ' . auth()->user()->school->name : '' }}
                </p>
            </div>
        </div>

        <!-- Quick Action Card -->
        <div class="card mb-4 border-warning">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-clipboard-plus" style="font-size: 3rem; color: #d97706;"></i>
                </div>
                <h5 class="card-title">Catat Pelanggaran Baru</h5>
                <p class="text-muted small mb-3">Laporkan pelanggaran yang ditemukan saat bertugas</p>
                <a href="{{ route('violations.create') }}" class="btn btn-warning btn-lg px-5">
                    <i class="bi bi-plus-lg me-2"></i>Catat Pelanggaran
                </a>
            </div>
        </div>

        <!-- PKS Team Stats & Quick Access -->
        <div class="row g-3 mb-4">
            <!-- Today's Duty Status -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-header py-2">
                        <h6 class="mb-0"><i class="bi bi-calendar-check me-2 text-warning"></i>Piket Hari Ini</h6>
                    </div>
                    <div class="card-body">
                        @if($pksData['todayAssignments']->isNotEmpty())
                            @foreach($pksData['todayAssignments'] as $assignment)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <div class="fw-bold">{{ $assignment->schedule->shift->name ?? 'Shift' }}</div>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $assignment->location->name ?? '-' }}
                                        </small>
                                    </div>
                                    @if($assignment->attendance)
                                        <span class="badge {{ $assignment->attendance->status_badge_class ?? 'bg-secondary' }}">
                                            {{ $assignment->attendance->status_display ?? 'Hadir' }}
                                        </span>
                                    @else
                                        <a href="{{ route('pks-duty-attendances.create', ['assignment_id' => $assignment->id]) }}"
                                           class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-clipboard-check me-1"></i>Isi Absensi
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2 small">Tidak ada jadwal piket hari ini</p>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer py-2">
                        <a href="{{ route('pks-duty-schedules.index') }}" class="small text-decoration-none">
                            <i class="bi bi-arrow-right me-1"></i>Lihat Semua Jadwal
                        </a>
                    </div>
                </div>
            </div>

            <!-- Violation Stats -->
            <div class="col-6 col-md-3 col-lg-2">
                <div class="card text-center h-100">
                    <div class="card-body py-3">
                        <div class="h3 mb-0 text-warning">{{ $pksData['violationStats']['today'] }}</div>
                        <small class="text-muted">Hari Ini</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <div class="card text-center h-100">
                    <div class="card-body py-3">
                        <div class="h3 mb-0 text-primary">{{ $pksData['violationStats']['week'] }}</div>
                        <small class="text-muted">Minggu Ini</small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-header py-2">
                        <h6 class="mb-0"><i class="bi bi-clock-history me-2 text-info"></i>Riwayat Terbaru</h6>
                    </div>
                    <div class="card-body py-2">
                        @if($pksData['recentViolations']->isNotEmpty())
                            @foreach($pksData['recentViolations']->take(3) as $v)
                                <a href="{{ route('violations.show', $v) }}"
                                   class="d-flex justify-content-between align-items-center py-1 text-decoration-none text-dark">
                                    <div class="small text-truncate me-2">
                                        <span class="badge bg-secondary me-1">{{ $v->violationType->code }}</span>
                                        {{ $v->student->full_name }}
                                    </div>
                                    <small class="text-muted">{{ $v->created_at->format('d/m') }}</small>
                                </a>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-2 small">
                                Belum ada pelanggaran yang dicatat
                            </div>
                        @endif
                    </div>
                    <div class="card-footer py-2">
                        <a href="{{ route('violations.index') }}" class="small text-decoration-none">
                            <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Welcome Banner for other roles -->
        <div class="welcome-banner">
            <div class="welcome-banner-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="welcome-banner-content">
                <h2 class="welcome-banner-title">Selamat Datang, {{ auth()->user()->name }}!</h2>
                <p class="welcome-banner-text">
                    @if(auth()->user()->isSuperAdmin())
                        Anda login sebagai Super Admin. Anda memiliki akses ke semua sekolah.
                    @elseif(auth()->user()->school)
                        Mengelola <strong>{{ auth()->user()->school->name }}</strong>
                    @else
                        Silakan hubungi administrator untuk menetapkan sekolah Anda.
                    @endif
                </p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-primary">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['activeStudents'] ?? 0 }}</div>
                    <div class="stat-label">Siswa Aktif</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['totalViolations'] ?? 0 }}</div>
                    <div class="stat-label">Total Pelanggaran</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-success">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['totalPksMembers'] ?? 0 }}</div>
                    <div class="stat-label">Anggota PKS</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-info">
                    <i class="bi bi-calendar-week"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['totalSchedules'] ?? 0 }}</div>
                    <div class="stat-label">Jadwal Piket</div>
                </div>
            </div>
        </div>
    @endif

    <div class="dashboard-grid">
        <!-- Quick Actions (for non-PKS roles) -->
        @if(!auth()->user()->isPksTeam())
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="dashboard-card-title">
                    <i class="bi bi-lightning-fill text-primary"></i>
                    Menu Cepat
                </div>
            </div>
            <div class="dashboard-card-body">
                <div class="quick-actions">
                    <a href="{{ route('students.index') }}" class="quick-action">
                        <div class="quick-action-icon" style="background: #e0e7ff; color: #4f46e5;">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <span class="quick-action-label">Daftar Siswa</span>
                    </a>
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                        <a href="{{ route('violations.index') }}" class="quick-action">
                            <div class="quick-action-icon" style="background: #fef3c7; color: #d97706;">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <span class="quick-action-label">Pelanggaran</span>
                        </a>
                        <a href="{{ route('violation-types.index') }}" class="quick-action">
                            <div class="quick-action-icon" style="background: #fee2e2; color: #dc2626;">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                            <span class="quick-action-label">Jenis Pelanggaran</span>
                        </a>
                    @endif
                    <a href="{{ route('pks-members.index') }}" class="quick-action">
                        <div class="quick-action-icon" style="background: #d1fae5; color: #059669;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <span class="quick-action-label">Anggota PKS</span>
                    </a>
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                        <a href="{{ route('pks-shifts.index') }}" class="quick-action">
                            <div class="quick-action-icon" style="background: #cffafe; color: #0891b2;">
                                <i class="bi bi-clock"></i>
                            </div>
                            <span class="quick-action-label">Shift Piket</span>
                        </a>
                        <a href="{{ route('pks-duty-schedules.index') }}" class="quick-action">
                            <div class="quick-action-icon" style="background: #f1f5f9; color: #64748b;">
                                <i class="bi bi-calendar-week"></i>
                            </div>
                            <span class="quick-action-label">Jadwal Piket</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Account Info -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="dashboard-card-title">
                    <i class="bi bi-info-circle-fill text-primary"></i>
                    Informasi Akun
                </div>
            </div>
            <div class="dashboard-card-body">
                <div class="account-info">
                    <div class="account-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="account-details">
                        <h5 class="account-name">{{ auth()->user()->name }}</h5>
                        <p class="account-email">{{ auth()->user()->email }}</p>
                        @if(auth()->user()->isPksTeam())
                            <span class="badge badge-warning">Tim PKS</span>
                        @else
                            <span class="badge badge-{{ auth()->user()->isSuperAdmin() ? 'danger' : (auth()->user()->isSchoolAdmin() ? 'primary' : 'secondary') }}">
                                {{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}
                            </span>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                <dl class="info-list">
                    <div class="info-row">
                        <dt><i class="bi bi-building"></i> Sekolah</dt>
                        <dd>
                            @if(auth()->user()->school)
                                {{ auth()->user()->school->name }}
                            @else
                                <span class="text-muted">Tidak Ditetapkan</span>
                            @endif
                        </dd>
                    </div>
                </dl>

                <hr class="my-4">

                <div class="action-buttons">
                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-person me-1"></i>Edit Profil
                    </a>
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('schools.index') }}" class="btn btn-sm btn-danger">
                            <i class="bi bi-building me-1"></i>Kelola Sekolah
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    /* PKS Team Welcome Styling */
    .pks-welcome {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border: 1px solid #fcd34d;
    }

    /* Mobile Responsive for Dashboard */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }

        .dashboard-grid {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr !important;
        }

        .stat-card {
            flex-direction: column !important;
            text-align: center !important;
        }

        .stat-icon {
            margin: 0 auto !important;
        }

        .welcome-banner {
            flex-direction: column !important;
            text-align: center !important;
            padding: 20px !important;
        }

        .quick-actions {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
</style>
