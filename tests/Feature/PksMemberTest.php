<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\PksMember;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PksMemberTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected School $school2;
    protected User $superAdmin;
    protected User $schoolAdmin;
    protected User $operator;
    protected User $teacher;
    protected Student $student;
    protected AcademicYear $academicYear;
    protected SchoolClass $class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::factory()->create();
        $this->school2 = School::factory()->create();

        $this->academicYear = AcademicYear::factory()->for($this->school)->create();
        $this->class = SchoolClass::factory()->for($this->school)->for($this->academicYear)->create();

        $this->student = Student::factory()
            ->for($this->school)
            ->for($this->academicYear)
            ->for($this->class)
            ->create();

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

    // ==========================================
    // CRUD Tests
    // ==========================================

    public function test_can_list_pks_members(): void
    {
        PksMember::factory()->for($this->student)->for($this->school)->create();

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-members.index'));

        $response->assertStatus(200);
        $response->assertSee($this->student->full_name);
    }

    public function test_can_create_pks_member(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-members.store'), [
            'student_id' => $this->student->id,
            'position' => PksMember::POSITION_MEMBER,
            'status' => 'active',
            'joined_at' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('pks-members.index'));
        $this->assertDatabaseHas('pks_members', [
            'student_id' => $this->student->id,
            'position' => PksMember::POSITION_MEMBER,
            'status' => 'active',
        ]);
    }

    public function test_can_view_pks_member_detail(): void
    {
        $member = PksMember::factory()->for($this->student)->for($this->school)->create();

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-members.show', $member));

        $response->assertStatus(200);
        $response->assertSee($this->student->full_name);
        $response->assertSee($member->position);
    }

    public function test_can_update_pks_member(): void
    {
        $member = PksMember::factory()->for($this->student)->for($this->school)->create([
            'position' => PksMember::POSITION_MEMBER,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->patch(route('pks-members.update', $member), [
            'position' => PksMember::POSITION_KORLAP,
            'status' => 'active',
            'joined_at' => $member->joined_at->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('pks-members.show', $member));
        $this->assertDatabaseHas('pks_members', [
            'id' => $member->id,
            'position' => PksMember::POSITION_KORLAP,
        ]);
    }

    // ==========================================
    // Authorization Tests
    // ==========================================

    public function test_operator_cannot_create_pks_member(): void
    {
        $response = $this->actingAs($this->operator)->post(route('pks-members.store'), [
            'student_id' => $this->student->id,
            'position' => PksMember::POSITION_MEMBER,
            'status' => 'active',
            'joined_at' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create_pks_member(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('pks-members.store'), [
            'student_id' => $this->student->id,
            'position' => PksMember::POSITION_MEMBER,
            'status' => 'active',
            'joined_at' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }

    public function test_operator_cannot_update_pks_member(): void
    {
        $member = PksMember::factory()->for($this->student)->for($this->school)->create();

        $response = $this->actingAs($this->operator)->patch(route('pks-members.update', $member), [
            'position' => PksMember::POSITION_KORLAP,
            'status' => 'active',
            'joined_at' => $member->joined_at->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }

    public function test_all_authenticated_users_can_view_pks_members(): void
    {
        PksMember::factory()->for($this->student)->for($this->school)->create();

        $this->assertTrue($this->operator->can('viewAny', PksMember::class));
        $this->assertTrue($this->teacher->can('viewAny', PksMember::class));
    }

    // ==========================================
    // Tenant Isolation Tests
    // ==========================================

    public function test_tenant_isolation_cannot_access_other_school_member(): void
    {
        $memberSchool2 = PksMember::factory()->for($this->school2)->create();

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-members.show', $memberSchool2));

        $response->assertStatus(404);
    }

    public function test_tenant_isolation_cannot_create_with_other_school_student(): void
    {
        $studentSchool2 = Student::factory()->for($this->school2)->create();

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-members.store'), [
            'student_id' => $studentSchool2->id,
            'position' => PksMember::POSITION_MEMBER,
            'status' => 'active',
            'joined_at' => now()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('student_id');
    }

    // ==========================================
    // Duplicate Prevention Tests
    // ==========================================

    public function test_cannot_create_duplicate_active_membership(): void
    {
        // Create first membership
        PksMember::factory()->for($this->student)->for($this->school)->create([
            'status' => PksMember::STATUS_ACTIVE,
        ]);

        // Try to create second active membership
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-members.store'), [
            'student_id' => $this->student->id,
            'position' => PksMember::POSITION_KORLAP,
            'status' => 'active',
            'joined_at' => now()->format('Y-m-d'),
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, PksMember::where('student_id', $this->student->id)->count());
    }

    public function test_can_create_membership_after_previous_became_inactive(): void
    {
        // Create first membership and make it inactive
        PksMember::factory()->for($this->student)->for($this->school)->create([
            'status' => PksMember::STATUS_INACTIVE,
        ]);

        // Create new active membership
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-members.store'), [
            'student_id' => $this->student->id,
            'position' => PksMember::POSITION_KORLAP,
            'status' => 'active',
            'joined_at' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('pks-members.index'));
    }

    // ==========================================
    // Status Tests
    // ==========================================

    public function test_status_active(): void
    {
        $member = PksMember::factory()->for($this->student)->for($this->school)->create([
            'status' => PksMember::STATUS_ACTIVE,
        ]);

        $this->assertTrue($member->isActive());
        $this->assertEquals('Aktif', $member->status_display);
    }

    public function test_status_inactive(): void
    {
        $member = PksMember::factory()->for($this->student)->for($this->school)->create([
            'status' => PksMember::STATUS_INACTIVE,
        ]);

        $this->assertFalse($member->isActive());
        $this->assertEquals('Tidak Aktif', $member->status_display);
    }

    public function test_status_graduated(): void
    {
        $member = PksMember::factory()->for($this->student)->for($this->school)->create([
            'status' => PksMember::STATUS_GRADUATED,
        ]);

        $this->assertEquals('Lulus', $member->status_display);
    }

    public function test_status_resigned(): void
    {
        $member = PksMember::factory()->for($this->student)->for($this->school)->create([
            'status' => PksMember::STATUS_RESIGNED,
        ]);

        $this->assertEquals('Mengundurkan Diri', $member->status_display);
    }

    // ==========================================
    // Search and Filter Tests
    // ==========================================

    public function test_can_search_by_student_name(): void
    {
        $student = Student::factory()
            ->for($this->school)
            ->for($this->academicYear)
            ->for($this->class)
            ->create(['full_name' => 'Budi Santoso']);

        PksMember::factory()->for($student)->for($this->school)->create();

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-members.index', ['search' => 'Budi']));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
    }

    public function test_can_search_by_nis(): void
    {
        $student = Student::factory()
            ->for($this->school)
            ->for($this->academicYear)
            ->for($this->class)
            ->create(['nis' => '99999']);

        PksMember::factory()->for($student)->for($this->school)->create();

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-members.index', ['search' => '99999']));

        $response->assertStatus(200);
        $response->assertSee('99999');
    }

    public function test_can_filter_by_status(): void
    {
        PksMember::factory()->for($this->student)->for($this->school)->create([
            'status' => PksMember::STATUS_ACTIVE,
        ]);

        $student2 = Student::factory()->for($this->school)->for($this->academicYear)->for($this->class)->create();
        PksMember::factory()->for($student2)->for($this->school)->create([
            'status' => PksMember::STATUS_INACTIVE,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-members.index', ['status' => 'active']));

        $response->assertStatus(200);
        $response->assertSee($this->student->full_name);
        $response->assertDontSee($student2->full_name);
    }

    public function test_can_filter_by_position(): void
    {
        PksMember::factory()->for($this->student)->for($this->school)->create([
            'position' => PksMember::POSITION_KETUA,
        ]);

        $student2 = Student::factory()->for($this->school)->for($this->academicYear)->for($this->class)->create();
        PksMember::factory()->for($student2)->for($this->school)->create([
            'position' => PksMember::POSITION_MEMBER,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-members.index', ['position' => PksMember::POSITION_KETUA]));

        $response->assertStatus(200);
        $response->assertSee(PksMember::POSITION_KETUA);
    }

    // ==========================================
    // Date Tests
    // ==========================================

    public function test_joined_at_required(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-members.store'), [
            'student_id' => $this->student->id,
            'position' => PksMember::POSITION_MEMBER,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('joined_at');
    }

    public function test_ended_at_after_joined_at_validation(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-members.store'), [
            'student_id' => $this->student->id,
            'position' => PksMember::POSITION_MEMBER,
            'status' => 'active',
            'joined_at' => now()->format('Y-m-d'),
            'ended_at' => now()->subDay()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('ended_at');
    }

    // ==========================================
    // Student Relationship Tests
    // ==========================================

    public function test_student_has_pks_member_relationship(): void
    {
        $member = PksMember::factory()->for($this->student)->for($this->school)->create();

        $this->assertTrue($this->student->pksMember()->exists());
        $this->assertEquals($member->id, $this->student->pksMember->id);
    }

    public function test_is_pks_member_check(): void
    {
        $this->assertFalse($this->student->isPksMember());

        PksMember::factory()->for($this->student)->for($this->school)->create([
            'status' => PksMember::STATUS_ACTIVE,
        ]);

        $this->assertTrue($this->student->fresh()->isPksMember());
    }
}
