<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationEvidence;
use App\Models\ViolationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViolationTypeTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected School $school2;
    protected User $superAdmin;
    protected User $schoolAdmin;
    protected User $operator;
    protected User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::factory()->create();
        $this->school2 = School::factory()->create();
        $academicYear = AcademicYear::factory()->for($this->school)->create();
        $department = Department::factory()->for($this->school)->create();

        $this->superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'school_id' => null,
        ]);

        $this->schoolAdmin = User::factory()->create([
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $this->school->id,
        ]);

        $this->operator = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'school_id' => $this->school->id,
        ]);

        $this->teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
            'school_id' => $this->school->id,
        ]);
    }

    public function test_authenticated_user_can_view_violation_types(): void
    {
        $type = ViolationType::factory()->for($this->school)->create();

        $response = $this->actingAs($this->schoolAdmin)->get(route('violation-types.index'));

        $response->assertStatus(200);
        $response->assertSee($type->code);
    }

    public function test_school_admin_can_create_violation_type(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('violation-types.store'), [
            'code' => 'LATE',
            'name' => 'Terlambat Masuk',
            'category' => 'Attendance',
            'severity' => 'low',
            'points' => 2,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('violation-types.index'));
        $this->assertDatabaseHas('violation_types', [
            'code' => 'LATE',
            'name' => 'Terlambat Masuk',
            'school_id' => $this->school->id,
        ]);
    }

    public function test_school_admin_can_update_violation_type(): void
    {
        $type = ViolationType::factory()->for($this->school)->create(['points' => 2]);

        $response = $this->actingAs($this->schoolAdmin)->patch(route('violation-types.update', $type), [
            'code' => $type->code,
            'name' => 'Updated Name',
            'category' => $type->category,
            'severity' => $type->severity,
            'points' => 5,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('violation-types.show', $type));
        $this->assertDatabaseHas('violation_types', [
            'id' => $type->id,
            'name' => 'Updated Name',
            'points' => 5,
        ]);
    }

    public function test_duplicate_code_rejected_within_same_school(): void
    {
        ViolationType::factory()->for($this->school)->create(['code' => 'LATE']);

        $response = $this->actingAs($this->schoolAdmin)->post(route('violation-types.store'), [
            'code' => 'LATE',
            'name' => 'Another Late',
            'category' => 'Attendance',
            'severity' => 'low',
            'points' => 2,
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_same_code_allowed_in_different_schools(): void
    {
        // Create violation type with code 'LATE' in school1
        ViolationType::factory()->for($this->school)->create(['code' => 'LATE']);

        // Create admin for school2
        $adminSchool2 = User::factory()->create([
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $this->school2->id,
        ]);

        // School2 admin should be able to create same code
        $response = $this->actingAs($adminSchool2)->post(route('violation-types.store'), [
            'code' => 'LATE',
            'name' => 'Late Different School',
            'category' => 'Attendance',
            'severity' => 'low',
            'points' => 2,
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_operator_cannot_create_violation_type(): void
    {
        $response = $this->actingAs($this->operator)->post(route('violation-types.store'), [
            'code' => 'TEST',
            'name' => 'Test',
            'category' => 'Other',
            'severity' => 'low',
            'points' => 1,
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create_violation_type(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('violation-types.store'), [
            'code' => 'TEST',
            'name' => 'Test',
            'category' => 'Other',
            'severity' => 'low',
            'points' => 1,
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_can_view_violation_types(): void
    {
        ViolationType::factory()->for($this->school)->create();

        $response = $this->actingAs($this->teacher)->get(route('violation-types.index'));

        $response->assertStatus(200);
    }

    public function test_tenant_isolation_violation_types(): void
    {
        $school2 = School::factory()->create();
        $typeSchool2 = ViolationType::factory()->for($school2)->create();

        // School admin should not see other school's violation types
        $response = $this->actingAs($this->schoolAdmin)->get(route('violation-types.index'));

        $response->assertDontSee($typeSchool2->code);
    }

    public function test_can_deactivate_violation_type(): void
    {
        $type = ViolationType::factory()->for($this->school)->create(['is_active' => true]);

        $this->actingAs($this->schoolAdmin)->patch(route('violation-types.update', $type), [
            'code' => $type->code,
            'name' => $type->name,
            'category' => $type->category,
            'severity' => $type->severity,
            'points' => $type->points,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('violation_types', [
            'id' => $type->id,
            'is_active' => false,
        ]);
    }

    public function test_cannot_delete_used_violation_type(): void
    {
        $type = ViolationType::factory()->for($this->school)->create();
        $student = Student::factory()->for($this->school)->create();
        Violation::factory()->for($this->school)->for($type)->forStudent($student)->create();

        $response = $this->actingAs($this->schoolAdmin)->delete(route('violation-types.destroy', $type));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('violation_types', ['id' => $type->id]);
    }

    public function test_can_delete_unused_violation_type(): void
    {
        $type = ViolationType::factory()->for($this->school)->create();

        $response = $this->actingAs($this->schoolAdmin)->delete(route('violation-types.destroy', $type));

        $response->assertRedirect(route('violation-types.index'));
        $this->assertDatabaseMissing('violation_types', ['id' => $type->id]);
    }

    public function test_can_filter_violation_types_by_category(): void
    {
        $attendanceType = ViolationType::factory()->for($this->school)->create(['category' => 'Attendance', 'code' => 'ATT', 'name' => 'Attendance Violation']);
        $behaviorType = ViolationType::factory()->for($this->school)->create(['category' => 'Behavior', 'code' => 'BEH', 'name' => 'Behavior Violation']);

        $response = $this->actingAs($this->schoolAdmin)->get(route('violation-types.index', ['category' => 'Attendance']));

        $response->assertStatus(200);
        $response->assertSee('ATT');
        $response->assertDontSee('BEH');
    }

    public function test_can_filter_violation_types_by_severity(): void
    {
        ViolationType::factory()->for($this->school)->create(['severity' => 'low', 'code' => 'LOW', 'name' => 'Low Severity']);
        ViolationType::factory()->for($this->school)->create(['severity' => 'high', 'code' => 'HIGH', 'name' => 'High Severity']);

        $response = $this->actingAs($this->schoolAdmin)->get(route('violation-types.index', ['severity' => 'low']));

        $response->assertStatus(200);
        $response->assertSee('LOW');
        $response->assertDontSee('HIGH');
    }

    public function test_can_search_violation_types(): void
    {
        ViolationType::factory()->for($this->school)->create(['code' => 'LATE', 'name' => 'Terlambat']);
        ViolationType::factory()->for($this->school)->create(['code' => 'HELMET', 'name' => 'Tidak Helm']);

        $response = $this->actingAs($this->schoolAdmin)->get(route('violation-types.index', ['search' => 'LATE']));

        $response->assertStatus(200);
        $response->assertSee('LATE');
        $response->assertDontSee('HELMET');
    }
}
