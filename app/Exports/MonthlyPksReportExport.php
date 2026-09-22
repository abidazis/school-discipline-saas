<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MonthlyPksReportExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        protected array $report,
        protected $school
    ) {}

    public function sheets(): array
    {
        return [
            new class($this->report, $this->school, 'Ringkasan') extends MonthlySheet {},
            new class($this->report, $this->school, 'Jadwal') extends MonthlySheet {},
            new class($this->report, $this->school, 'Penugasan') extends MonthlySheet {},
            new class($this->report, $this->school, 'Kehadiran') extends MonthlySheet {},
            new class($this->report, $this->school, 'Aktivitas') extends MonthlySheet {},
            new class($this->report, $this->school, 'Pelanggaran') extends MonthlySheet {},
        ];
    }
}

class MonthlySheet implements WithHeadings, WithStyles, FromCollection
{
    use Exportable;

    public function __construct(
        protected array $report,
        protected $school,
        protected string $sheetName
    ) {}

    public function collection(): Collection
    {
        return match ($this->sheetName) {
            'Ringkasan' => $this->getRingkasanData(),
            'Jadwal' => $this->getJadwalData(),
            'Penugasan' => $this->getPenugasanData(),
            'Kehadiran' => $this->getKehadiranData(),
            'Aktivitas' => $this->getAktivitasData(),
            'Pelanggaran' => $this->getPelanggaranData(),
            default => collect(),
        };
    }

    public function headings(): array
    {
        return match ($this->sheetName) {
            'Ringkasan' => ['Metrik', 'Nilai'],
            'Jadwal' => ['Tanggal', 'Shift', 'Jam Mulai', 'Jam Selesai', 'Status', 'Jumlah Lokasi'],
            'Penugasan' => ['Tanggal', 'Shift', 'Petugas', 'NIS', 'Posisi', 'Lokasi', 'Status'],
            'Kehadiran' => ['Tanggal', 'Shift', 'Petugas', 'Lokasi', 'Status', 'Check In', 'Check Out'],
            'Aktivitas' => ['Tanggal', 'Jam Mulai', 'Jam Selesai', 'Petugas', 'Lokasi', 'Jenis', 'Temuan', 'Tindakan', 'Status'],
            'Pelanggaran' => ['Tanggal', 'Waktu', 'Siswa', 'NIS', 'Jenis', 'Poin', 'Status'],
            default => [],
        };
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('1:' . $sheet->getHighestRow())->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        return [];
    }

    protected function getRingkasanData(): Collection
    {
        $data = collect([
            ['PERIODE', $this->report['period']],
            ['', ''],
            ['RINGKASAN', ''],
            ['Total Jadwal', $this->report['summary']['total_schedules']],
            ['Total Penugasan', $this->report['summary']['total_assignments']],
            ['Total Kehadiran', $this->report['summary']['total_attendances']],
            ['Hadir', $this->report['summary']['present_count']],
            ['Terlambat', $this->report['summary']['late_count']],
            ['Tidak Hadir', $this->report['summary']['absent_count']],
            ['Izin', $this->report['summary']['excused_count']],
            ['Belum Diisi', $this->report['summary']['not_recorded_count']],
            ['', ''],
            ['Total Aktivitas', $this->report['summary']['total_activities']],
            ['Total Pelanggaran', $this->report['summary']['total_violations']],
            ['Total Poin Pelanggaran', $this->report['summary']['total_points']],
        ]);

        return $data;
    }

    protected function getJadwalData(): Collection
    {
        $data = collect();

        foreach ($this->report['schedules'] as $schedule) {
            $data->push([
                $schedule->schedule_date->format('d/m/Y'),
                $schedule->shift?->name ?? '-',
                $schedule->shift?->start_time ?? '-',
                $schedule->shift?->end_time ?? '-',
                $schedule->status_display,
                $schedule->locations->count(),
            ]);
        }

        return $data;
    }

    protected function getPenugasanData(): Collection
    {
        $data = collect();

        foreach ($this->report['schedules'] as $schedule) {
            foreach ($schedule->assignments as $assignment) {
                $data->push([
                    $schedule->schedule_date->format('d/m/Y'),
                    $schedule->shift?->name ?? '-',
                    $assignment->member?->student?->full_name ?? '-',
                    $assignment->member?->student?->nis ?? '-',
                    $assignment->member?->position ?? '-',
                    $assignment->location?->name ?? '-',
                    $assignment->status_display,
                ]);
            }
        }

        return $data;
    }

    protected function getKehadiranData(): Collection
    {
        $data = collect();

        foreach ($this->report['schedules'] as $schedule) {
            foreach ($schedule->assignments as $assignment) {
                $attendance = $assignment->attendance;
                $data->push([
                    $schedule->schedule_date->format('d/m/Y'),
                    $schedule->shift?->name ?? '-',
                    $assignment->member?->student?->full_name ?? '-',
                    $assignment->location?->name ?? '-',
                    $attendance?->status_display ?? 'Belum Diisi',
                    $attendance?->check_in_at?->format('H:i') ?? '-',
                    $attendance?->check_out_at?->format('H:i') ?? '-',
                ]);
            }
        }

        return $data;
    }

    protected function getAktivitasData(): Collection
    {
        $data = collect();

        foreach ($this->report['activities'] as $activity) {
            $data->push([
                $activity->activity_date->format('d/m/Y'),
                $activity->started_at,
                $activity->ended_at ?? '-',
                $activity->assignment?->member?->student?->full_name ?? '-',
                $activity->assignment?->location?->name ?? '-',
                $activity->activity_type_label,
                $activity->finding ?? '-',
                $activity->action_taken ?? '-',
                $activity->status_display,
            ]);
        }

        return $data;
    }

    protected function getPelanggaranData(): Collection
    {
        $data = collect();

        foreach ($this->report['violations'] as $violation) {
            $data->push([
                $violation->occurred_at?->format('d/m/Y') ?? '-',
                $violation->occurred_at?->format('H:i') ?? '-',
                $violation->student?->full_name ?? '-',
                $violation->student?->nis ?? '-',
                $violation->violationType?->name ?? '-',
                $violation->points ?? 0,
                $violation->status_display,
            ]);
        }

        return $data;
    }
}
