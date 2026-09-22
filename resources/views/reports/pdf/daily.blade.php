<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian PKS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10px;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #f0f0f0;
            padding: 8px;
            font-weight: bold;
            border: 1px solid #ccc;
            margin-bottom: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 5px 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f8f8f8;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        .badge-info { background-color: #d1ecf1; color: #0c5460; }
        .badge-secondary { background-color: #e2e3e5; color: #383d41; }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .summary-item {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        .summary-item .value {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .summary-item .label {
            font-size: 9px;
            color: #666;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            display: flex;
            justify-content: space-between;
        }
        .signature-block {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .page-number {
            text-align: right;
            font-size: 9px;
            color: #666;
            margin-top: 10px;
        }
        @page {
            margin: 15mm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN HARIAN KEGIATAN PKS</h1>
        <h2>{{ $school?->name ?? 'SMK Negeri' }}</h2>
        <p>Tanggal: {{ $date->format('d F Y') }}</p>
    </div>

    <div class="summary-grid">
        <div class="summary-item">
            <div class="value">{{ $report['summary']['total_schedules'] }}</div>
            <div class="label">Total Jadwal</div>
        </div>
        <div class="summary-item">
            <div class="value">{{ $report['summary']['total_assignments'] }}</div>
            <div class="label">Total Petugas</div>
        </div>
        <div class="summary-item">
            <div class="value" style="color: #28a745;">{{ $report['summary']['present_count'] }}</div>
            <div class="label">Hadir</div>
        </div>
        <div class="summary-item">
            <div class="value" style="color: #ffc107;">{{ $report['summary']['late_count'] }}</div>
            <div class="label">Terlambat</div>
        </div>
        <div class="summary-item">
            <div class="value" style="color: #dc3545;">{{ $report['summary']['absent_count'] }}</div>
            <div class="label">Tidak Hadir</div>
        </div>
        <div class="summary-item">
            <div class="value" style="color: #17a2b8;">{{ $report['summary']['excused_count'] }}</div>
            <div class="label">Izin</div>
        </div>
    </div>

    @if($report['schedules']->count() > 0)
        <div class="section">
            <div class="section-title">JADWAL PIKET</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 15%;">Shift</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 60%;">Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['schedules'] as $schedule)
                        <tr>
                            <td>{{ $schedule->schedule_date->format('d/m/Y') }}</td>
                            <td>{{ $schedule->shift?->name ?? '-' }} ({{ $schedule->time_range ?? '-' }})</td>
                            <td class="text-center">
                                @if($schedule->status === 'scheduled')
                                    <span class="badge badge-info">Terjadwal</span>
                                @elseif($schedule->status === 'completed')
                                    <span class="badge badge-success">Selesai</span>
                                @else
                                    <span class="badge badge-secondary">Dibatalkan</span>
                                @endif
                            </td>
                            <td>
                                @foreach($schedule->locations as $location)
                                    {{ $location->name }}@if(!$loop->last), @endif
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($report['schedules']->pluck('assignments')->flatten()->count() > 0)
        <div class="section">
            <div class="section-title">DAFTAR PETUGAS & KEHADIRAN</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 20%;">Petugas</th>
                        <th style="width: 10%;">NIS</th>
                        <th style="width: 15%;">Posisi</th>
                        <th style="width: 15%;">Lokasi</th>
                        <th style="width: 15%;">Status Kehadiran</th>
                        <th style="width: 10%;">Check In</th>
                        <th style="width: 10%;">Check Out</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($report['schedules'] as $schedule)
                        @foreach($schedule->assignments as $assignment)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ $assignment->member?->student?->full_name ?? '-' }}</td>
                                <td class="text-center">{{ $assignment->member?->student?->nis ?? '-' }}</td>
                                <td class="text-center">{{ $assignment->member?->position ?? '-' }}</td>
                                <td>{{ $assignment->location?->name ?? '-' }}</td>
                                <td class="text-center">
                                    @if($assignment->attendance)
                                        @if($assignment->attendance->status === 'present')
                                            <span class="badge badge-success">Hadir</span>
                                        @elseif($assignment->attendance->status === 'late')
                                            <span class="badge badge-warning">Terlambat</span>
                                        @elseif($assignment->attendance->status === 'absent')
                                            <span class="badge badge-danger">Tidak Hadir</span>
                                        @elseif($assignment->attendance->status === 'excused')
                                            <span class="badge badge-info">Izin</span>
                                        @endif
                                    @else
                                        <span class="badge badge-secondary">Belum Diisi</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $assignment->attendance?->check_in_at?->format('H:i') ?? '-' }}</td>
                                <td class="text-center">{{ $assignment->attendance?->check_out_at?->format('H:i') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($report['activities']->count() > 0)
        <div class="section">
            <div class="section-title">AKTIVITAS LAPANGAN</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 12%;">Waktu</th>
                        <th style="width: 20%;">Petugas</th>
                        <th style="width: 15%;">Lokasi</th>
                        <th style="width: 10%;">Jenis</th>
                        <th style="width: 23%;">Temuan</th>
                        <th style="width: 20%;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['activities'] as $activity)
                        <tr>
                            <td>{{ $activity->started_at }}@if($activity->ended_at) - {{ $activity->ended_at }}@endif</td>
                            <td>{{ $activity->assignment?->member?->student?->full_name ?? '-' }}</td>
                            <td>{{ $activity->assignment?->location?->name ?? '-' }}</td>
                            <td>{{ $activity->activity_type_label }}</td>
                            <td>{{ Str::limit($activity->finding, 50) ?: '-' }}</td>
                            <td>{{ Str::limit($activity->action_taken, 40) ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($report['violations']->count() > 0)
        <div class="section">
            <div class="section-title">PELANGGARAN</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Waktu</th>
                        <th style="width: 25%;">Siswa</th>
                        <th style="width: 10%;">NIS</th>
                        <th style="width: 25%;">Jenis Pelanggaran</th>
                        <th style="width: 10%;">Poin</th>
                        <th style="width: 15%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['violations'] as $violation)
                        <tr>
                            <td>{{ $violation->occurred_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td>{{ $violation->student?->full_name ?? '-' }}</td>
                            <td>{{ $violation->student?->nis ?? '-' }}</td>
                            <td>{{ $violation->violationType?->name ?? '-' }}</td>
                            <td class="text-center">{{ $violation->points ?? 0 }}</td>
                            <td class="text-center">
                                @if($violation->status === 'verified')
                                    <span class="badge badge-success">Terverifikasi</span>
                                @else
                                    <span class="badge badge-secondary">Tercatat</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <div class="signature-block">
            <div class="signature-line">Koordinator PKS</div>
        </div>
        <div class="signature-block">
            <div class="signature-line">Pembina/Koordinator</div>
        </div>
    </div>

    <div class="page-number">
        Dicetak: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
