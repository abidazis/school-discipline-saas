<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\PksDutyAssignment;
use App\Models\PksDutyAttendance;
use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksMember;
use App\Models\PksShift;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PksDutyAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $schoolAdmin;
    protected User $operator;
    protected User $teacher;
    protected School $school;
    protected PksDutySchedule $schedule;
    protected PksMember $member;
    protected PksDutyLocation $location;
    protected PksDutyAssignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        // Create school
        $this->school = School::factory()->create();

        // Create academic year
        AcademicYear::factory()->create([
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        // Create department and class
        $department = Department::factory()->create(['school_id' => $this->school->id]);
        $class = SchoolClass::factory()->create([
            'school_id' => $this->school->id,
            'department_id' => $department->id,
        ]);

        // Create student
        $student = Student::factory()->create([
            'school_id' => $this->school->id,
            'school_class_id' => $class->id,
        ]);

        // Create shift
        $shift = PksShift::factory()->create(['school_id' => $this->school->id]);

        // Create location
        $this->location = PksDutyLocation::factory()->create(['school_id' => $this->school->id]);

        // Create schedule with location
        $this->schedule = PksDutySchedule::factory()->create([
            'school_id' => $this->school->id,
            'pks_shift_id' => $shift->id,
            'status' => PksDutySchedule::STATUS_SCHEDULED,
        ]);
        $this->schedule->locations()->attach($this->location->id);

        // Create PKS member
        $this->member = PksMember::factory()->create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'status' => PksMember::STATUS_ACTIVE,
        ]);

        // Create assignment
        $this->assignment = PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => PksDutyAssignment::STATUS_ASSIGNED,
        ]);

        // Create users
        $this->schoolAdmin = User::factory()->create([
            'school_id' => $this->school->id,
            'role' => 'school_admin',
        ]);

        $this->operator = User::factory()->create([
            'school_id' => $this->school->id,
            'role' => 'operator',
        ]);

        $this->teacher = User::factory()->create([
            'school_id' => $this->school->id,
            'role' => 'teacher',
        ]);
    }

    // ==================== CRUD Tests ====================

    public function test_authorized_user_can_view_attendances(): void
    {
        PksDutyAttendance::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-attendances.index'));

        $response->assertStatus(200);
        $response->assertSee('Kehadiran Piket');
    }

    public function test_authorized_user_can_create_attendance(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
            'check_in_at' => now()->format('Y-m-d\TH:i'),
            'check_out_at' => now()->addHours(3)->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_attendances', [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);
    }

    public function test_authorized_user_can_view_attendance_detail(): void
    {
        $attendance = PksDutyAttendance::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-attendances.show', $attendance));

        $response->assertStatus(200);
        $response->assertSee('Detail Kehadiran');
    }

    public function test_authorized_user_can_update_attendance(): void
    {
        $attendance = PksDutyAttendance::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->put(route('pks-duty-attendances.update', $attendance), [
            'status' => 'late',
            'notes' => 'Terlambat karena macet',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_attendances', [
            'id' => $attendance->id,
            'status' => 'late',
            'notes' => 'Terlambat karena macet',
        ]);
    }

    // ==================== Authorization Tests ====================

    public function test_teacher_cannot_create_attendance(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_update_attendance(): void
    {
        $attendance = PksDutyAttendance::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->teacher)->put(route('pks-duty-attendances.update', $attendance), [
            'status' => 'late',
        ]);

        $response->assertStatus(403);
    }

    public function test_operator_can_create_attendance(): void
    {
        $response = $this->actingAs($this->operator)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_attendances', [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);
    }

    public function test_operator_can_update_attendance(): void
    {
        $attendance = PksDutyAttendance::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->operator)->put(route('pks-duty-attendances.update', $attendance), [
            'status' => 'late',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_attendances', [
            'id' => $attendance->id,
            'status' => 'late',
        ]);
    }

    // ==================== Tenant Isolation Tests ====================

    public function test_school_a_cannot_view_school_b_attendance(): void
    {
        $schoolB = School::factory()->create();

        $shiftB = PksShift::factory()->create(['school_id' => $schoolB->id]);
        $locationB = PksDutyLocation::factory()->create(['school_id' => $schoolB->id]);
        $studentB = Student::factory()->create(['school_id' => $schoolB->id]);
        $memberB = PksMember::factory()->create([
            'school_id' => $schoolB->id,
            'student_id' => $studentB->id,
        ]);

        $scheduleB = PksDutySchedule::factory()->create([
            'school_id' => $schoolB->id,
            'pks_shift_id' => $shiftB->id,
        ]);
        $scheduleB->locations()->attach($locationB->id);

        $assignmentB = PksDutyAssignment::factory()->create([
            'school_id' => $schoolB->id,
            'pks_duty_schedule_id' => $scheduleB->id,
            'pks_member_id' => $memberB->id,
            'pks_duty_location_id' => $locationB->id,
        ]);

        $attendanceB = PksDutyAttendance::factory()->create([
            'school_id' => $schoolB->id,
            'pks_duty_assignment_id' => $assignmentB->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-attendances.show', $attendanceB));

        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    public function test_school_a_cannot_create_attendance_using_school_b_assignment(): void
    {
        $schoolB = School::factory()->create();

        $shiftB = PksShift::factory()->create(['school_id' => $schoolB->id]);
        $locationB = PksDutyLocation::factory()->create(['school_id' => $schoolB->id]);
        $studentB = Student::factory()->create(['school_id' => $schoolB->id]);
        $memberB = PksMember::factory()->create([
            'school_id' => $schoolB->id,
            'student_id' => $studentB->id,
        ]);

        $scheduleB = PksDutySchedule::factory()->create([
            'school_id' => $schoolB->id,
            'pks_shift_id' => $shiftB->id,
        ]);
        $scheduleB->locations()->attach($locationB->id);

        $assignmentB = PksDutyAssignment::factory()->create([
            'school_id' => $schoolB->id,
            'pks_duty_schedule_id' => $scheduleB->id,
            'pks_member_id' => $memberB->id,
            'pks_duty_location_id' => $locationB->id,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $assignmentB->id,
            'status' => 'present',
        ]);

        $response->assertSessionHasErrors(['pks_duty_assignment_id']);
    }

    public function test_tenant_cannot_create_attendance_using_other_tenant_assignment(): void
    {
        $schoolB = School::factory()->create();

        $shiftB = PksShift::factory()->create(['school_id' => $schoolB->id]);
        $locationB = PksDutyLocation::factory()->create(['school_id' => $schoolB->id]);
        $studentB = Student::factory()->create(['school_id' => $schoolB->id]);
        $memberB = PksMember::factory()->create([
            'school_id' => $schoolB->id,
            'student_id' => $studentB->id,
        ]);

        $scheduleB = PksDutySchedule::factory()->create([
            'school_id' => $schoolB->id,
            'pks_shift_id' => $shiftB->id,
        ]);
        $scheduleB->locations()->attach($locationB->id);

        $assignmentB = PksDutyAssignment::factory()->create([
            'school_id' => $schoolB->id,
            'pks_duty_schedule_id' => $scheduleB->id,
            'pks_member_id' => $memberB->id,
            'pks_duty_location_id' => $locationB->id,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $assignmentB->id,
            'status' => 'present',
        ]);

        $response->assertSessionHasErrors(['pks_duty_assignment_id']);
        $this->assertEquals(0, PksDutyAttendance::count());
    }

    // ==================== Assignment Integrity Tests ====================

    public function test_attendance_requires_valid_assignment(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => 99999,
            'status' => 'present',
        ]);

        $response->assertSessionHasErrors(['pks_duty_assignment_id']);
    }

    public function test_cancelled_assignment_cannot_receive_attendance(): void
    {
        $this->assignment->update(['status' => PksDutyAssignment::STATUS_CANCELLED]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        $response->assertSessionHasErrors(['pks_duty_assignment_id']);
    }

    public function test_replaced_assignment_cannot_receive_new_attendance(): void
    {
        $this->assignment->update(['status' => PksDutyAssignment::STATUS_REPLACED]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        $response->assertSessionHasErrors(['pks_duty_assignment_id']);
    }

    public function test_cancelled_schedule_cannot_receive_attendance(): void
    {
        $this->schedule->update(['status' => PksDutySchedule::STATUS_CANCELLED]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        $response->assertSessionHasErrors(['pks_duty_assignment_id']);
    }

    // ==================== Attendance Rules Tests ====================

    public function test_present_with_check_in_works(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
            'check_in_at' => now()->format('Y-m-d\TH:i'),
            'check_out_at' => now()->addHours(3)->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_attendances', [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);
    }

    public function test_late_with_check_in_works(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'late',
            'check_in_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_attendances', [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'late',
        ]);
    }

    public function test_absent_without_check_in_works(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'absent',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_attendances', [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'absent',
        ]);
    }

    public function test_excused_without_check_in_works(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'excused',
            'notes' => 'Izin keluarga',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_attendances', [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'excused',
            'notes' => 'Izin keluarga',
        ]);
    }

    public function test_check_out_before_check_in_fails(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
            'check_in_at' => now()->format('Y-m-d\TH:i'),
            'check_out_at' => now()->subHour()->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors(['check_out_at']);
    }

    public function test_duplicate_attendance_for_same_assignment_fails(): void
    {
        PksDutyAttendance::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        $response->assertSessionHasErrors(['pks_duty_assignment_id']);
    }

    public function test_check_in_check_out_timestamps_persist(): void
    {
        $checkIn = now()->format('Y-m-d\TH:i');
        $checkOut = now()->addHours(3)->format('Y-m-d\TH:i');

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
            'check_in_at' => $checkIn,
            'check_out_at' => $checkOut,
        ]);

        $response->assertRedirect();

        $attendance = PksDutyAttendance::first();
        $this->assertNotNull($attendance->check_in_at);
        $this->assertNotNull($attendance->check_out_at);
    }

    // ==================== Tenant Isolation Tests ====================

    public function test_same_student_names_across_schools_do_not_conflict(): void
    {
        $schoolB = School::factory()->create();

        $studentB = Student::factory()->create([
            'school_id' => $schoolB->id,
            'full_name' => $this->member->student->full_name,
        ]);
        $memberB = PksMember::factory()->create([
            'school_id' => $schoolB->id,
            'student_id' => $studentB->id,
        ]);

        $shiftB = PksShift::factory()->create(['school_id' => $schoolB->id]);
        $locationB = PksDutyLocation::factory()->create(['school_id' => $schoolB->id]);
        $scheduleB = PksDutySchedule::factory()->create([
            'school_id' => $schoolB->id,
            'pks_shift_id' => $shiftB->id,
        ]);
        $scheduleB->locations()->attach($locationB->id);

        $assignmentB = PksDutyAssignment::factory()->create([
            'school_id' => $schoolB->id,
            'pks_duty_schedule_id' => $scheduleB->id,
            'pks_member_id' => $memberB->id,
            'pks_duty_location_id' => $locationB->id,
        ]);

        // School A can create attendance for their assignment
        $responseA = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        $responseA->assertRedirect();
        $this->assertDatabaseHas('pks_duty_attendances', [
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $this->assignment->id,
        ]);
    }

    // ==================== Schedule Detail Tests ====================

    public function test_attendance_appears_under_correct_location(): void
    {
        PksDutyAttendance::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-schedules.show', $this->schedule));

        $response->assertStatus(200);
        $response->assertSee('Kehadiran Petugas');
        $response->assertSee($this->member->student->full_name);
    }

    public function test_attendance_summary_counts_correctly(): void
    {
        // Create more assignments
        $student2 = Student::factory()->create(['school_id' => $this->school->id]);
        $member2 = PksMember::factory()->create([
            'school_id' => $this->school->id,
            'student_id' => $student2->id,
        ]);

        $assignment2 = PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $member2->id,
            'pks_duty_location_id' => $this->location->id,
        ]);

        // Create attendance for first assignment
        PksDutyAttendance::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);

        // Create attendance for second assignment
        PksDutyAttendance::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $assignment2->id,
            'status' => 'late',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-schedules.show', $this->schedule));

        $response->assertStatus(200);
        $response->assertSee('Total Petugas');
        $response->assertSee('Hadir');
        $response->assertSee('Terlambat');
    }

    public function test_no_attendance_appears_as_belum_disi(): void
    {
        // Assignment exists but no attendance
        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-schedules.show', $this->schedule));

        $response->assertStatus(200);
        $response->assertSee('Belum Diisi');
    }

    // ==================== Super Admin Tests ====================

    public function test_super_admin_can_manage_attendances(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($superAdmin)->get(route('pks-duty-attendances.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($superAdmin)->get(route('pks-duty-attendances.create'));
        $response->assertStatus(200);

        $response = $this->actingAs($superAdmin)->post(route('pks-duty-attendances.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'present',
        ]);
        $response->assertRedirect();
    }
}
