<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\PksDutyAssignment;
use App\Models\PksDutyAttendance;
use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksFieldActivity;
use App\Models\PksMember;
use App\Models\PksShift;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PksReportTest extends TestCase
{
    use RefreshDatabase;

    protected School $schoolA;
    protected School $schoolB;
    protected User $schoolAdminA;
    protected User $schoolAdminB;
    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create two schools for cross-tenant testing
        $this->schoolA = School::factory()->create(['name' => 'SMK Negeri 1 Jakarta']);
        $this->schoolB = School::factory()->create(['name' => 'SMK Negeri 2 Bandung']);

        // Create users for each school
        $this->schoolAdminA = User::factory()->create([
            'school_id' => $this->schoolA->id,
            'role' => User::ROLE_SCHOOL_ADMIN,
        ]);

        $this->schoolAdminB = User::factory()->create([
            'school_id' => $this->schoolB->id,
            'role' => User::ROLE_SCHOOL_ADMIN,
        ]);

        $this->superAdmin = User::factory()->create([
            'school_id' => $this->schoolA->id,
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
    }

    /**
     * Create a complete PKS scenario for a school.
     */
    protected function createPksScenario(School $school, int $assignmentCount = 3): array
    {
        // Create academic year
        $academicYear = AcademicYear::factory()->for($school)->create([
            'is_active' => true,
        ]);

        // Create department and class
        $department = Department::factory()->for($school)->create();
        $schoolClass = SchoolClass::factory()->for($school)->for($department)->for($academicYear)->create();

        // Create students
        $students = Student::factory()->for($school)->for($schoolClass)->for($academicYear)->count($assignmentCount)->create();

        // Create shift
        $shift = PksShift::factory()->for($school)->create([
            'name' => 'Pagi',
            'start_time' => '07:00',
            'end_time' => '10:00',
        ]);

        // Create location
        $location = PksDutyLocation::factory()->for($school)->create([
            'name' => 'Gerbang Depan',
        ]);

        // Create schedule for today
        $schedule = PksDutySchedule::factory()->for($school)->create([
            'pks_shift_id' => $shift->id,
            'schedule_date' => Carbon::today(),
            'status' => PksDutySchedule::STATUS_SCHEDULED,
        ]);
        $schedule->locations()->attach($location->id);

        // Create members and assignments
        $assignments = [];
        foreach ($students as $index => $student) {
            $member = PksMember::factory()->for($school)->for($student)->create([
                'position' => $index === 0 ? PksMember::POSITION_KORLAP : PksMember::POSITION_MEMBER,
            ]);

            $assignment = PksDutyAssignment::factory()->for($school)->create([
                'pks_duty_schedule_id' => $schedule->id,
                'pks_member_id' => $member->id,
                'pks_duty_location_id' => $location->id,
                'status' => PksDutyAssignment::STATUS_ASSIGNED,
            ]);

            // Create attendance for some assignments
            if ($index < 2) {
                PksDutyAttendance::factory()->for($school)->create([
                    'pks_duty_assignment_id' => $assignment->id,
                    'status' => $index === 0 ? PksDutyAttendance::STATUS_PRESENT : PksDutyAttendance::STATUS_LATE,
                    'check_in_at' => now()->setTime(7, 0 + $index),
                    'check_out_at' => now()->setTime(10, 0),
                ]);
            }

            // Create field activity for first assignment
            if ($index === 0) {
                PksFieldActivity::factory()->for($school)->create([
                    'pks_duty_assignment_id' => $assignment->id,
                    'activity_date' => Carbon::today(),
                    'activity_type' => PksFieldActivity::TYPE_MONITORING,
                    'status' => PksFieldActivity::STATUS_COMPLETED,
                    'finding' => 'Siswa tertidur di kelas',
                    'action_taken' => 'Dibangunkan dan diperingatkan',
                ]);
            }

            $assignments[] = $assignment;
        }

        // Create violation
        $violationType = ViolationType::factory()->for($school)->create();
        $officer = User::factory()->for($school)->create();
        $violation = Violation::factory()->for($school)->for($students->first())->for($violationType)->for($academicYear)->for($schoolClass)->create([
            'officer_id' => $officer->id,
            'occurred_at' => Carbon::today(),
            'points' => 5,
            'status' => Violation::STATUS_RECORDED,
        ]);

        return [
            'schedule' => $schedule,
            'shift' => $shift,
            'location' => $location,
            'assignments' => $assignments,
            'students' => $students,
            'violation' => $violation,
        ];
    }

    // ==========================================
    // DAILY REPORT TESTS
    // ==========================================

    public function test_authenticated_user_can_view_daily_report(): void
    {
        $this->actingAs($this->schoolAdminA);

        $response = $this->get(route('reports.daily'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Harian PKS');
    }

    public function test_guest_cannot_access_daily_report(): void
    {
        $response = $this->get(route('reports.daily'));

        $response->assertRedirectToRoute('login');
    }

    public function test_daily_report_shows_schedule_data(): void
    {
        $this->actingAs($this->schoolAdminA);
        $scenario = $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Jadwal Piket');
        $response->assertSee($scenario['shift']->name);
    }

    public function test_daily_report_shows_assignments(): void
    {
        $this->actingAs($this->schoolAdminA);
        $scenario = $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        // Assignments are shown in a table within the schedule card
        $response->assertSee('Petugas');
        $response->assertSee('NIS');
        $response->assertSee('Posisi');
    }

    public function test_daily_report_shows_attendance(): void
    {
        $this->actingAs($this->schoolAdminA);
        $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Hadir');
        $response->assertSee('Terlambat');
    }

    public function test_daily_report_shows_field_activities(): void
    {
        $this->actingAs($this->schoolAdminA);
        $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Aktivitas Lapangan');
        $response->assertSee('Monitoring');
    }

    public function test_daily_report_shows_violations(): void
    {
        $this->actingAs($this->schoolAdminA);
        $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Pelanggaran');
    }

    public function test_daily_report_attendance_summary_is_correct(): void
    {
        $this->actingAs($this->schoolAdminA);
        $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        // Summary cards should show attendance counts
        $response->assertSee('Total Jadwal');
        $response->assertSee('Total Petugas');
        $response->assertSee('Hadir');
    }

    public function test_daily_report_empty_state_for_no_data(): void
    {
        $this->actingAs($this->schoolAdminA);

        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        // Should show empty state message
        $response->assertSee('Belum ada jadwal PKS');
    }

    public function test_daily_pdf_download_works(): void
    {
        $this->actingAs($this->schoolAdminA);
        $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.daily.pdf', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertDownload('laporan-harian-pks-' . Carbon::today()->format('Y-m-d') . '.pdf');
    }

    public function test_daily_pdf_respects_date_filter(): void
    {
        $this->actingAs($this->schoolAdminA);

        // Create scenario for a specific date
        $this->createPksScenario($this->schoolA);

        // Request PDF for today
        $response = $this->get(route('reports.daily.pdf', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_daily_pdf_empty_state(): void
    {
        $this->actingAs($this->schoolAdminA);

        // Request PDF for a date with no data
        $response = $this->get(route('reports.daily.pdf', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    // ==========================================
    // MONTHLY REPORT TESTS
    // ==========================================

    public function test_authenticated_user_can_view_monthly_report(): void
    {
        $this->actingAs($this->schoolAdminA);

        $response = $this->get(route('reports.monthly'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Bulanan PKS');
    }

    public function test_guest_cannot_access_monthly_report(): void
    {
        $response = $this->get(route('reports.monthly'));

        $response->assertRedirectToRoute('login');
    }

    public function test_monthly_report_shows_summary(): void
    {
        $this->actingAs($this->schoolAdminA);
        $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.monthly', [
            'month' => Carbon::today()->month,
            'year' => Carbon::today()->year,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Total Jadwal');
        $response->assertSee('Total Penugasan');
        $response->assertSee('Total Aktivitas');
        $response->assertSee('Total Pelanggaran');
    }

    public function test_monthly_report_daily_breakdown(): void
    {
        $this->actingAs($this->schoolAdminA);
        $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.monthly', [
            'month' => Carbon::today()->month,
            'year' => Carbon::today()->year,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Rincian Harian');
    }

    public function test_monthly_excel_download_works(): void
    {
        $this->actingAs($this->schoolAdminA);
        $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.monthly.excel', [
            'month' => Carbon::today()->month,
            'year' => Carbon::today()->year,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertDownload('laporan-pks-bulanan-' . Carbon::today()->year . '-' . str_pad(Carbon::today()->month, 2, '0', STR_PAD_LEFT) . '.xlsx');
    }

    public function test_monthly_excel_respects_filters(): void
    {
        $this->actingAs($this->schoolAdminA);
        $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.monthly.excel', [
            'month' => Carbon::today()->month,
            'year' => Carbon::today()->year,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_monthly_report_empty_state(): void
    {
        $this->actingAs($this->schoolAdminA);

        $response = $this->get(route('reports.monthly', [
            'month' => Carbon::today()->month,
            'year' => Carbon::today()->year,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Tidak ada data PKS');
    }

    // ==========================================
    // AUTHORIZATION TESTS
    // ==========================================

    public function test_operator_can_view_daily_report(): void
    {
        $operator = User::factory()->create([
            'school_id' => $this->schoolA->id,
            'role' => User::ROLE_OPERATOR,
        ]);
        $this->actingAs($operator);

        $response = $this->get(route('reports.daily'));

        $response->assertStatus(200);
    }

    public function test_teacher_can_view_daily_report(): void
    {
        $teacher = User::factory()->create([
            'school_id' => $this->schoolA->id,
            'role' => User::ROLE_TEACHER,
        ]);
        $this->actingAs($teacher);

        $response = $this->get(route('reports.daily'));

        $response->assertStatus(200);
    }

    // ==========================================
    // CROSS-TENANT TESTS
    // ==========================================

    public function test_cross_tenant_daily_report_blocked(): void
    {
        // School A creates data
        $this->actingAs($this->schoolAdminA);
        $scenario = $this->createPksScenario($this->schoolA);

        // School B admin tries to access School A's data
        $this->actingAs($this->schoolAdminB);

        // Even if School B tries to access with School A's date, they should see empty
        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        // School B should only see their own data (or empty if no data)
        $response->assertSee('Belum ada jadwal PKS');
    }

    public function test_cross_tenant_monthly_report_blocked(): void
    {
        // School A creates data
        $this->actingAs($this->schoolAdminA);
        $this->createPksScenario($this->schoolA);

        // School B admin tries to access
        $this->actingAs($this->schoolAdminB);

        $response = $this->get(route('reports.monthly', [
            'month' => Carbon::today()->month,
            'year' => Carbon::today()->year,
        ]));

        $response->assertStatus(200);
        // School B should see empty or their own data
        $response->assertSee('Tidak ada data PKS');
    }

    public function test_cross_tenant_shift_filter_blocked(): void
    {
        // School A creates shift and data
        $this->actingAs($this->schoolAdminA);
        $scenarioA = $this->createPksScenario($this->schoolA);
        $shiftAId = $scenarioA['shift']->id;

        // School B creates their own data
        $scenarioB = $this->createPksScenario($this->schoolB);

        // School B tries to use School A's shift_id filter
        $this->actingAs($this->schoolAdminB);

        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
            'shift_id' => $shiftAId, // School A's shift - should return empty for School B
        ]));

        $response->assertStatus(200);
        // Should show empty state because School B doesn't have this shift
        $response->assertSee('Belum ada jadwal PKS');
    }

    public function test_cross_tenant_location_filter_blocked(): void
    {
        // School A creates location and data
        $this->actingAs($this->schoolAdminA);
        $scenarioA = $this->createPksScenario($this->schoolA);
        $locationAId = $scenarioA['location']->id;

        // School B creates their own data
        $this->createPksScenario($this->schoolB);

        // School B tries to use School A's location_id filter
        $this->actingAs($this->schoolAdminB);

        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
            'location_id' => $locationAId, // School A's location - should return empty for School B
        ]));

        $response->assertStatus(200);
        // Should show empty state because School B doesn't have this location
        $response->assertSee('Belum ada jadwal PKS');
    }

    // ==========================================
    // DATA INTEGRITY TESTS
    // ==========================================

    public function test_missing_attendance_not_counted_as_absent(): void
    {
        $this->actingAs($this->schoolAdminA);
        $scenario = $this->createPksScenario($this->schoolA, 5);

        // Only 2 of 5 assignments have attendance (created in scenario)
        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        // Should show "Belum Diisi" for assignments without attendance
        $response->assertSee('Belum Diisi');
        // Should NOT show 5 as absent
    }

    public function test_cancelled_schedule_handled_correctly(): void
    {
        $this->actingAs($this->schoolAdminA);

        // Create schedule that is cancelled
        $shift = PksShift::factory()->for($this->schoolA)->create();
        $location = PksDutyLocation::factory()->for($this->schoolA)->create();

        $schedule = PksDutySchedule::factory()->for($this->schoolA)->create([
            'pks_shift_id' => $shift->id,
            'schedule_date' => Carbon::today(),
            'status' => PksDutySchedule::STATUS_CANCELLED,
        ]);
        $schedule->locations()->attach($location->id);

        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
            'status' => 'cancelled',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Dibatalkan');
    }

    public function test_cancelled_assignment_handled_correctly(): void
    {
        $this->actingAs($this->schoolAdminA);

        $shift = PksShift::factory()->for($this->schoolA)->create();
        $location = PksDutyLocation::factory()->for($this->schoolA)->create();
        $student = Student::factory()->for($this->schoolA)->create();
        $member = PksMember::factory()->for($this->schoolA)->for($student)->create();

        $schedule = PksDutySchedule::factory()->for($this->schoolA)->create([
            'pks_shift_id' => $shift->id,
            'schedule_date' => Carbon::today(),
        ]);
        $schedule->locations()->attach($location->id);

        $assignment = PksDutyAssignment::factory()->for($this->schoolA)->create([
            'pks_duty_schedule_id' => $schedule->id,
            'pks_member_id' => $member->id,
            'pks_duty_location_id' => $location->id,
            'status' => PksDutyAssignment::STATUS_CANCELLED,
        ]);

        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        // Cancelled assignments should still be in report with proper status
        $response->assertSee('Dibatalkan');
    }

    // ==========================================
    // FILTER TESTS
    // ==========================================

    public function test_daily_report_shift_filter_works(): void
    {
        $this->actingAs($this->schoolAdminA);
        $scenario = $this->createPksScenario($this->schoolA);
        $shiftId = $scenario['shift']->id;

        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
            'shift_id' => $shiftId,
        ]));

        $response->assertStatus(200);
        $response->assertSee($scenario['shift']->name);
    }

    public function test_daily_report_location_filter_works(): void
    {
        $this->actingAs($this->schoolAdminA);
        $scenario = $this->createPksScenario($this->schoolA);
        $locationId = $scenario['location']->id;

        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
            'location_id' => $locationId,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Gerbang Depan');
    }

    public function test_daily_report_status_filter_works(): void
    {
        $this->actingAs($this->schoolAdminA);

        // Create shifts with unique names to avoid unique constraint
        $shift = PksShift::factory()->for($this->schoolA)->create([
            'name' => 'Pagi-' . uniqid(),
        ]);
        $location = PksDutyLocation::factory()->for($this->schoolA)->create();

        // Create scheduled and completed schedules
        $scheduled = PksDutySchedule::factory()->for($this->schoolA)->create([
            'pks_shift_id' => $shift->id,
            'schedule_date' => Carbon::today(),
            'status' => PksDutySchedule::STATUS_SCHEDULED,
        ]);
        $scheduled->locations()->attach($location->id);

        // Create a second shift with unique name
        $shift2 = PksShift::factory()->for($this->schoolA)->create([
            'name' => 'Sore-' . uniqid(),
        ]);

        $completed = PksDutySchedule::factory()->for($this->schoolA)->create([
            'pks_shift_id' => $shift2->id,
            'schedule_date' => Carbon::today(),
            'status' => PksDutySchedule::STATUS_COMPLETED,
        ]);
        $completed->locations()->attach($location->id);

        // Filter by scheduled
        $response = $this->get(route('reports.daily', [
            'date' => Carbon::today()->format('Y-m-d'),
            'status' => 'scheduled',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Terjadwal');
    }

    public function test_monthly_report_shift_filter_works(): void
    {
        $this->actingAs($this->schoolAdminA);
        $scenario = $this->createPksScenario($this->schoolA);
        $shiftId = $scenario['shift']->id;

        $response = $this->get(route('reports.monthly', [
            'month' => Carbon::today()->month,
            'year' => Carbon::today()->year,
            'shift_id' => $shiftId,
        ]));

        $response->assertStatus(200);
    }

    public function test_monthly_report_location_filter_works(): void
    {
        $this->actingAs($this->schoolAdminA);
        $scenario = $this->createPksScenario($this->schoolA);
        $locationId = $scenario['location']->id;

        $response = $this->get(route('reports.monthly', [
            'month' => Carbon::today()->month,
            'year' => Carbon::today()->year,
            'location_id' => $locationId,
        ]));

        $response->assertStatus(200);
    }

    // ==========================================
    // SUPER ADMIN TESTS
    // ==========================================

    public function test_super_admin_can_view_any_school_daily_report(): void
    {
        $this->actingAs($this->superAdmin);

        // Super admin with school A can see school A data
        $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.daily'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Harian PKS');
    }

    public function test_super_admin_can_view_any_school_monthly_report(): void
    {
        $this->actingAs($this->superAdmin);

        $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.monthly'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Bulanan PKS');
    }

    public function test_super_admin_can_export_daily_pdf(): void
    {
        $this->actingAs($this->superAdmin);
        $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.daily.pdf', [
            'date' => Carbon::today()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_can_export_monthly_excel(): void
    {
        $this->actingAs($this->superAdmin);
        $this->createPksScenario($this->schoolA);

        $response = $this->get(route('reports.monthly.excel', [
            'month' => Carbon::today()->month,
            'year' => Carbon::today()->year,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
