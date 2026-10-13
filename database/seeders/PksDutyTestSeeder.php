<?php

namespace Database\Seeders;

use App\Models\PksDutyAssignment;
use App\Models\PksDutyAttendance;
use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksMember;
use App\Models\PksShift;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PksDutyTestSeeder extends Seeder
{
    /**
     * Seed PKS duty data for testing.
     *
     * Creates:
     * - Shifts (Pagi, Siang)
     * - Locations (Gerbang Depan, Gerbang Belakang, Lobby)
     * - Schedules for today and next 7 days
     * - Assignments for all active PKS members
     * - Sample attendance records
     */
    public function run(): void
    {
        // Get school
        $school = School::where('slug', 'smk-negeri-1-jakarta')->first();
        if (!$school) {
            $this->command->warn('School SMK Negeri 1 Jakarta not found. Skipping PKS duty seeder.');
            return;
        }

        $this->command->info('Seeding PKS duty data for testing...');

        // ========================================
        // SHIFTS
        // ========================================
        $shifts = [
            [
                'name' => 'PAGI',
                'start_time' => '06:00:00',
                'end_time' => '09:00:00',
            ],
            [
                'name' => 'SIANG',
                'start_time' => '12:00:00',
                'end_time' => '15:00:00',
            ],
        ];

        foreach ($shifts as $shiftData) {
            PksShift::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'name' => $shiftData['name'],
                ],
                $shiftData
            );
        }
        $this->command->info('Created shifts: PAGI, SIANG');

        // ========================================
        // LOCATIONS
        // ========================================
        $locations = [
            ['name' => 'Gerbang Depan', 'code' => 'DEPAN', 'description' => 'Pos utama depan sekolah'],
            ['name' => 'Gerbang Belakang', 'code' => 'BELAKANG', 'description' => 'Pos belakang sekolah'],
            ['name' => 'Lobby Utama', 'code' => 'LOBBY', 'description' => 'Lobby gedung utama'],
        ];

        foreach ($locations as $locData) {
            PksDutyLocation::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'name' => $locData['name'],
                ],
                array_merge($locData, ['status' => PksDutyLocation::STATUS_ACTIVE])
            );
        }
        $this->command->info('Created locations: Gerbang Depan, Gerbang Belakang, Lobby Utama');

        // Get created records
        $shiftPagi = PksShift::where('school_id', $school->id)->where('name', 'PAGI')->first();
        $shiftSiang = PksShift::where('school_id', $school->id)->where('name', 'SIANG')->first();
        $locDepan = PksDutyLocation::where('school_id', $school->id)->where('name', 'Gerbang Depan')->first();
        $locBelakang = PksDutyLocation::where('school_id', $school->id)->where('name', 'Gerbang Belakang')->first();

        // Get active PKS members
        $pksMembers = PksMember::where('school_id', $school->id)
            ->where('status', PksMember::STATUS_ACTIVE)
            ->get();

        if ($pksMembers->isEmpty()) {
            $this->command->warn('No active PKS members found. Run PksTeamSeeder first.');
            return;
        }

        // ========================================
        // SCHEDULES & ASSIGNMENTS
        // ========================================
        $today = Carbon::today();

        // Create schedules for today and next 7 days
        for ($i = 0; $i <= 7; $i++) {
            $date = $today->copy()->addDays($i);

            // Morning shift schedule
            $schedulePagi = PksDutySchedule::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'pks_shift_id' => $shiftPagi->id,
                    'schedule_date' => $date->toDateString(),
                ],
                [
                    'status' => $i === 0 ? PksDutySchedule::STATUS_SCHEDULED : PksDutySchedule::STATUS_SCHEDULED,
                    'notes' => "Jadwal piket {$date->format('d/m/Y')} shift pagi",
                ]
            );

            // Afternoon shift schedule
            $scheduleSiang = PksDutySchedule::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'pks_shift_id' => $shiftSiang->id,
                    'schedule_date' => $date->toDateString(),
                ],
                [
                    'status' => PksDutySchedule::STATUS_SCHEDULED,
                    'notes' => "Jadwal piket {$date->format('d/m/Y')} shift siang",
                ]
            );

            // Assign PKS members to morning shift (alternate between depan and belakang)
            foreach ($pksMembers as $index => $member) {
                $location = ($index % 2 === 0) ? $locDepan : $locBelakang;

                PksDutyAssignment::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'pks_duty_schedule_id' => $schedulePagi->id,
                        'pks_member_id' => $member->id,
                        'pks_duty_location_id' => $location->id,
                    ],
                    [
                        'status' => PksDutyAssignment::STATUS_ASSIGNED,
                        'assigned_at' => $date,
                    ]
                );
            }

            // Assign to afternoon shift (different pattern)
            foreach ($pksMembers as $index => $member) {
                $location = ($index % 2 === 1) ? $locDepan : $locBelakang;

                PksDutyAssignment::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'pks_duty_schedule_id' => $scheduleSiang->id,
                        'pks_member_id' => $member->id,
                        'pks_duty_location_id' => $location->id,
                    ],
                    [
                        'status' => PksDutyAssignment::STATUS_ASSIGNED,
                        'assigned_at' => $date,
                    ]
                );
            }
        }

        $this->command->info("Created schedules and assignments for today through " . $today->copy()->addDays(7)->format('d/m/Y'));

        // ========================================
        // SAMPLE ATTENDANCES (for past dates)
        // ========================================
        $pastSchedules = PksDutySchedule::where('school_id', $school->id)
            ->where('schedule_date', '<', $today->toDateString())
            ->limit(5)
            ->get();

        foreach ($pastSchedules as $schedule) {
            foreach ($schedule->assignments as $assignment) {
                // Skip if attendance already exists
                if ($assignment->attendance) {
                    continue;
                }

                // Random status for past dates
                $statuses = [
                    PksDutyAttendance::STATUS_PRESENT,
                    PksDutyAttendance::STATUS_PRESENT,
                    PksDutyAttendance::STATUS_PRESENT,
                    PksDutyAttendance::STATUS_LATE,
                    PksDutyAttendance::STATUS_ABSENT,
                ];
                $status = $statuses[array_rand($statuses)];

                $checkIn = null;
                $checkOut = null;

                if (in_array($status, [PksDutyAttendance::STATUS_PRESENT, PksDutyAttendance::STATUS_LATE])) {
                    $checkIn = $schedule->schedule_date->copy()->setTimeFromTimeString($schedule->shift->start_time);
                    if ($status === PksDutyAttendance::STATUS_LATE) {
                        $checkIn->addMinutes(rand(15, 30));
                    }
                    $checkOut = $checkIn->copy()->addHours(3);
                }

                PksDutyAttendance::create([
                    'school_id' => $school->id,
                    'pks_duty_assignment_id' => $assignment->id,
                    'status' => $status,
                    'check_in_at' => $checkIn,
                    'check_out_at' => $checkOut,
                    'notes' => $status === PksDutyAttendance::STATUS_LATE ? 'Terlambat karena macet' : null,
                    'recorded_by' => $assignment->member->user_id,
                ]);
            }
        }

        $this->command->info('Created sample attendance records for past dates');

        // Summary
        $this->command->info('');
        $this->command->info('=== PKS DUTY TEST DATA SUMMARY ===');
        $this->command->info('Shifts: ' . PksShift::where('school_id', $school->id)->count());
        $this->command->info('Locations: ' . PksDutyLocation::where('school_id', $school->id)->count());
        $this->command->info('Schedules: ' . PksDutySchedule::where('school_id', $school->id)->count());
        $this->command->info('Assignments: ' . PksDutyAssignment::where('school_id', $school->id)->count());
        $this->command->info('Attendances: ' . PksDutyAttendance::where('school_id', $school->id)->count());
        $this->command->info('');
        $this->command->info('PKS members can now:');
        $this->command->info('- View their schedules at /pks-duty-schedules');
        $this->command->info('- View their attendance at /pks-duty-attendances');
        $this->command->info('- Record attendance for today\'s assignments');
    }
}
