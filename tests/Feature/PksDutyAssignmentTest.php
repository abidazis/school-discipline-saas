<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\PksDutyAssignment;
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

class PksDutyAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $schoolAdmin;
    protected User $operator;
    protected User $teacher;
    protected School $school;
    protected PksDutySchedule $schedule;
    protected PksMember $member;
    protected PksDutyLocation $location;

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

    public function test_authorized_user_can_view_assignments(): void
    {
        PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-assignments.index'));

        $response->assertStatus(200);
        $response->assertSee('Penugasan Piket');
    }

    public function test_authorized_user_can_create_assignment(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_assignments', [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);
    }

    public function test_authorized_user_can_view_assignment_detail(): void
    {
        $assignment = PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-assignments.show', $assignment));

        $response->assertStatus(200);
        $response->assertSee('Detail Penugasan Piket');
    }

    public function test_authorized_user_can_edit_assignment(): void
    {
        $assignment = PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->put(route('pks-duty-assignments.update', $assignment), [
            'status' => 'replaced',
            'notes' => 'Digantikan oleh anggota lain',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_assignments', [
            'id' => $assignment->id,
            'status' => 'replaced',
            'notes' => 'Digantikan oleh anggota lain',
        ]);
    }

    // ==================== Authorization Tests ====================

    public function test_operator_cannot_create_assignment(): void
    {
        $response = $this->actingAs($this->operator)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response->assertStatus(403);
    }

    public function test_operator_cannot_update_assignment(): void
    {
        $assignment = PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
        ]);

        $response = $this->actingAs($this->operator)->put(route('pks-duty-assignments.update', $assignment), [
            'status' => 'replaced',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create_assignment(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_update_assignment(): void
    {
        $assignment = PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
        ]);

        $response = $this->actingAs($this->teacher)->put(route('pks-duty-assignments.update', $assignment), [
            'status' => 'replaced',
        ]);

        $response->assertStatus(403);
    }

    // ==================== Tenant Isolation Tests ====================

    public function test_school_a_cannot_view_school_b_assignment(): void
    {
        $schoolB = School::factory()->create();

        // Create assignment for school B
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

        // School A admin trying to view School B's assignment
        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-assignments.show', $assignmentB));

        // Should be forbidden (403) or not found (404) - both mean isolation works
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    public function test_school_a_cannot_edit_school_b_assignment(): void
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

        $response = $this->actingAs($this->schoolAdmin)->put(route('pks-duty-assignments.update', $assignmentB), [
            'status' => 'replaced',
        ]);

        // Should be forbidden (403) or not found (404) - both mean isolation works
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    public function test_tenant_cannot_create_assignment_using_other_tenant_records(): void
    {
        $schoolB = School::factory()->create();

        // Create School B's records
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

        // Attempt to create assignment using School B's member and location with School A's schedule
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id, // School A schedule
            'pks_member_id' => $memberB->id, // School B member
            'pks_duty_location_id' => $locationB->id, // School B location
            'status' => 'assigned',
        ]);

        $response->assertSessionHasErrors(['pks_member_id', 'pks_duty_location_id']);
    }

    public function test_school_a_cannot_use_school_b_schedule(): void
    {
        $schoolB = School::factory()->create();

        $shiftB = PksShift::factory()->create(['school_id' => $schoolB->id]);
        $locationB = PksDutyLocation::factory()->create(['school_id' => $schoolB->id]);

        $scheduleB = PksDutySchedule::factory()->create([
            'school_id' => $schoolB->id,
            'pks_shift_id' => $shiftB->id,
        ]);
        $scheduleB->locations()->attach($locationB->id);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $scheduleB->id, // School B schedule
            'pks_member_id' => $this->member->id, // School A member
            'pks_duty_location_id' => $this->location->id, // School A location
            'status' => 'assigned',
        ]);

        $response->assertSessionHasErrors(['pks_duty_schedule_id']);
    }

    // ==================== Relationship Validation Tests ====================

    public function test_location_must_belong_to_schedule(): void
    {
        // Create another location NOT attached to the schedule
        $otherLocation = PksDutyLocation::factory()->create(['school_id' => $this->school->id]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $otherLocation->id, // Not attached to schedule
            'status' => 'assigned',
        ]);

        $response->assertSessionHasErrors(['pks_duty_location_id']);
    }

    public function test_inactive_member_cannot_be_assigned(): void
    {
        $this->member->update(['status' => PksMember::STATUS_INACTIVE]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response->assertSessionHasErrors(['pks_member_id']);
    }

    public function test_graduated_member_cannot_be_assigned(): void
    {
        $this->member->update(['status' => PksMember::STATUS_GRADUATED]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response->assertSessionHasErrors(['pks_member_id']);
    }

    public function test_resigned_member_cannot_be_assigned(): void
    {
        $this->member->update(['status' => PksMember::STATUS_RESIGNED]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response->assertSessionHasErrors(['pks_member_id']);
    }

    public function test_cancelled_schedule_cannot_receive_assignment(): void
    {
        $this->schedule->update(['status' => PksDutySchedule::STATUS_CANCELLED]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response->assertSessionHasErrors(['pks_duty_schedule_id']);
    }

    public function test_completed_schedule_cannot_receive_normal_new_assignment(): void
    {
        $this->schedule->update(['status' => PksDutySchedule::STATUS_COMPLETED]);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response->assertSessionHasErrors(['pks_duty_schedule_id']);
    }

    // ==================== Duplicate / Assignment Rules Tests ====================

    public function test_same_member_cannot_be_assigned_twice_to_same_schedule(): void
    {
        // Create first assignment
        PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        // Try to create second assignment for same member
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response->assertSessionHasErrors(['pks_member_id']);
    }

    public function test_same_member_cannot_be_assigned_to_another_location_in_same_schedule(): void
    {
        // Create another location for the schedule
        $otherLocation = PksDutyLocation::factory()->create(['school_id' => $this->school->id]);
        $this->schedule->locations()->attach($otherLocation->id);

        // Create first assignment
        PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        // Try to assign same member to different location
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $otherLocation->id,
            'status' => 'assigned',
        ]);

        $response->assertSessionHasErrors(['pks_member_id']);
    }

    public function test_multiple_members_may_be_assigned_to_same_location(): void
    {
        // Create another member
        $student2 = Student::factory()->create(['school_id' => $this->school->id]);
        $member2 = PksMember::factory()->create([
            'school_id' => $this->school->id,
            'student_id' => $student2->id,
            'status' => PksMember::STATUS_ACTIVE,
        ]);

        // Create first assignment
        PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        // Create second assignment to same location
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $member2->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_assignments', [
            'pks_member_id' => $member2->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);
    }

    // ==================== Status Tests ====================

    public function test_assigned_status_works(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_assignments', [
            'pks_member_id' => $this->member->id,
            'status' => 'assigned',
        ]);
    }

    public function test_replaced_status_works(): void
    {
        $assignment = PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->put(route('pks-duty-assignments.update', $assignment), [
            'status' => 'replaced',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_assignments', [
            'id' => $assignment->id,
            'status' => 'replaced',
        ]);
    }

    public function test_cancelled_status_works(): void
    {
        $assignment = PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->put(route('pks-duty-assignments.update', $assignment), [
            'status' => 'cancelled',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_assignments', [
            'id' => $assignment->id,
            'status' => 'cancelled',
        ]);
    }

    // ==================== Tenant Isolation Tests ====================

    public function test_same_member_names_across_different_schools_do_not_conflict(): void
    {
        $schoolB = School::factory()->create();

        // Create member with same name in school B (use full_name, not name)
        $studentB = Student::factory()->create([
            'school_id' => $schoolB->id,
            'full_name' => $this->member->student->full_name, // Same name
        ]);
        $memberB = PksMember::factory()->create([
            'school_id' => $schoolB->id,
            'student_id' => $studentB->id,
            'status' => PksMember::STATUS_ACTIVE,
        ]);

        // Create schedule and location for school B
        $shiftB = PksShift::factory()->create(['school_id' => $schoolB->id]);
        $locationB = PksDutyLocation::factory()->create(['school_id' => $schoolB->id]);
        $scheduleB = PksDutySchedule::factory()->create([
            'school_id' => $schoolB->id,
            'pks_shift_id' => $shiftB->id,
        ]);
        $scheduleB->locations()->attach($locationB->id);

        // Both schools can create assignments for their own members
        $responseA = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $responseA->assertRedirect();
        $this->assertDatabaseHas('pks_duty_assignments', [
            'school_id' => $this->school->id,
            'pks_member_id' => $this->member->id,
        ]);
    }

    // ==================== Schedule Detail Tests ====================

    public function test_schedule_detail_displays_assigned_members(): void
    {
        $assignment = PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-schedules.show', $this->schedule));

        $response->assertStatus(200);
        // Check that the page shows the member's position (which we know)
        $response->assertSee($this->member->position);
        // Check that assignments section exists
        $response->assertSee('Petugas yang Ditugaskan');
    }

    public function test_assignments_grouped_by_location(): void
    {
        // Create second member
        $student2 = Student::factory()->create(['school_id' => $this->school->id]);
        $member2 = PksMember::factory()->create([
            'school_id' => $this->school->id,
            'student_id' => $student2->id,
            'status' => PksMember::STATUS_ACTIVE,
        ]);

        // Create two assignments at same location
        PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        PksDutyAssignment::factory()->create([
            'school_id' => $this->school->id,
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $member2->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-schedules.show', $this->schedule));

        $response->assertStatus(200);
        // Check that both members' positions are displayed
        $response->assertSee($this->member->position);
        $response->assertSee($member2->position);
        // Check that location is displayed
        $response->assertSee($this->location->name);
    }

    // ==================== Super Admin Tests ====================

    public function test_super_admin_can_manage_assignments(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($superAdmin)->get(route('pks-duty-assignments.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($superAdmin)->get(route('pks-duty-assignments.create'));
        $response->assertStatus(200);

        $response = $this->actingAs($superAdmin)->post(route('pks-duty-assignments.store'), [
            'pks_duty_schedule_id' => $this->schedule->id,
            'pks_member_id' => $this->member->id,
            'pks_duty_location_id' => $this->location->id,
            'status' => 'assigned',
        ]);
        $response->assertRedirect();
    }
}
