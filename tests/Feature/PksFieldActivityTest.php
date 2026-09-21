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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PksFieldActivityTest extends TestCase
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

    public function test_authorized_user_can_view_activities(): void
    {
        PksFieldActivity::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-field-activities.index'));

        $response->assertStatus(200);
        $response->assertSee('Aktivitas Lapangan');
    }

    public function test_authorized_user_can_create_activity(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '07:00',
            'ended_at' => '10:00',
            'activity_type' => 'monitoring',
            'description' => 'Melakukan monitoring kedatangan siswa.',
            'finding' => '5 siswa datang terlambat.',
            'action_taken' => 'Siswa diarahkan ke ruang PKS.',
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_field_activities', [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_type' => 'monitoring',
            'status' => 'completed',
        ]);
    }

    public function test_authorized_user_can_view_activity_detail(): void
    {
        $activity = PksFieldActivity::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-field-activities.show', $activity));

        $response->assertStatus(200);
        $response->assertSee('Detail Aktivitas Lapangan');
    }

    public function test_authorized_user_can_update_activity(): void
    {
        $activity = PksFieldActivity::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->put(route('pks-field-activities.update', $activity), [
            'status' => 'completed',
            'finding' => 'Updated finding',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_field_activities', [
            'id' => $activity->id,
            'status' => 'completed',
            'finding' => 'Updated finding',
        ]);
    }

    // ==================== Authorization Tests ====================

    public function test_teacher_cannot_create_activity(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '07:00',
            'activity_type' => 'monitoring',
            'status' => 'completed',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_update_activity(): void
    {
        $activity = PksFieldActivity::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $this->assignment->id,
        ]);

        $response = $this->actingAs($this->teacher)->put(route('pks-field-activities.update', $activity), [
            'status' => 'completed',
        ]);

        $response->assertStatus(403);
    }

    public function test_operator_can_create_activity(): void
    {
        $response = $this->actingAs($this->operator)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '07:00',
            'activity_type' => 'monitoring',
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_field_activities', [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_type' => 'monitoring',
        ]);
    }

    public function test_operator_can_update_activity(): void
    {
        $activity = PksFieldActivity::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_assignment_id' => $this->assignment->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->operator)->put(route('pks-field-activities.update', $activity), [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_field_activities', [
            'id' => $activity->id,
            'status' => 'completed',
        ]);
    }

    // ==================== Tenant Isolation Tests ====================

    public function test_school_a_cannot_view_school_b_activity(): void
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

        $activityB = PksFieldActivity::factory()->create([
            'school_id' => $schoolB->id,
            'pks_duty_assignment_id' => $assignmentB->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-field-activities.show', $activityB));

        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    public function test_school_a_cannot_create_using_school_b_assignment(): void
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

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $assignmentB->id,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '07:00',
            'activity_type' => 'monitoring',
            'status' => 'completed',
        ]);

        $response->assertSessionHasErrors(['pks_duty_assignment_id']);
    }

    // ==================== Assignment Integrity Tests ====================

    public function test_invalid_assignment_rejected(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => 99999,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '07:00',
            'activity_type' => 'monitoring',
        ]);

        $response->assertSessionHasErrors(['pks_duty_assignment_id']);
    }

    public function test_replaced_assignment_rejected(): void
    {
        $this->assignment->update(['status' => PksDutyAssignment::STATUS_REPLACED]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '07:00',
            'activity_type' => 'monitoring',
        ]);

        $response->assertSessionHasErrors(['pks_duty_assignment_id']);
    }

    public function test_cancelled_schedule_rejected(): void
    {
        $this->schedule->update(['status' => PksDutySchedule::STATUS_CANCELLED]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '07:00',
            'activity_type' => 'monitoring',
        ]);

        $response->assertSessionHasErrors(['pks_duty_assignment_id']);
    }

    public function test_valid_assignment_accepted(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '07:00',
            'activity_type' => 'monitoring',
            'status' => 'draft',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_field_activities', [
            'pks_duty_assignment_id' => $this->assignment->id,
        ]);
    }

    // ==================== Time Validation Tests ====================

    public function test_started_at_required(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_date' => now()->format('Y-m-d'),
            'activity_type' => 'monitoring',
        ]);

        $response->assertSessionHasErrors(['started_at']);
    }

    public function test_ended_at_before_started_at_fails(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '10:00',
            'ended_at' => '07:00',
            'activity_type' => 'monitoring',
        ]);

        $response->assertSessionHasErrors(['ended_at']);
    }

    // ==================== Violation Integration Tests ====================

    public function test_activity_without_violation_allowed(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '07:00',
            'activity_type' => 'monitoring',
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_field_activities', [
            'violation_id' => null,
        ]);
    }

    public function test_activity_with_valid_violation_allowed(): void
    {
        $violationType = ViolationType::factory()->create(['school_id' => $this->school->id]);
        $violation = Violation::factory()->create([
            'school_id' => $this->school->id,
            'violation_type_id' => $violationType->id,
            'student_id' => $this->member->student_id,
            'status' => 'recorded',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '07:00',
            'activity_type' => 'monitoring',
            'status' => 'completed',
            'violation_id' => $violation->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_field_activities', [
            'violation_id' => $violation->id,
        ]);
    }

    public function test_cross_tenant_violation_rejected(): void
    {
        $schoolB = School::factory()->create();

        $violationTypeB = ViolationType::factory()->create(['school_id' => $schoolB->id]);
        $violationB = Violation::factory()->create([
            'school_id' => $schoolB->id,
            'violation_type_id' => $violationTypeB->id,
            'status' => 'recorded',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '07:00',
            'activity_type' => 'monitoring',
            'status' => 'completed',
            'violation_id' => $violationB->id,
        ]);

        $response->assertSessionHasErrors(['violation_id']);
    }

    // ==================== Status Tests ====================

    public function test_draft_status_works(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '07:00',
            'activity_type' => 'monitoring',
            'status' => 'draft',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_field_activities', ['status' => 'draft']);
    }

    public function test_completed_status_works(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '07:00',
            'activity_type' => 'monitoring',
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_field_activities', ['status' => 'completed']);
    }

    // ==================== Super Admin Tests ====================

    public function test_super_admin_can_manage_activities(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($superAdmin)->get(route('pks-field-activities.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($superAdmin)->get(route('pks-field-activities.create'));
        $response->assertStatus(200);

        $response = $this->actingAs($superAdmin)->post(route('pks-field-activities.store'), [
            'pks_duty_assignment_id' => $this->assignment->id,
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => '07:00',
            'activity_type' => 'monitoring',
            'status' => 'completed',
        ]);
        $response->assertRedirect();
    }
}
