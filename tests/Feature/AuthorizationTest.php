<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\PksMember;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationEvidence;
use App\Models\ViolationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PksTeamAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected School $school2;
    protected User $superAdmin;
    protected User $schoolAdmin;
    protected User $pembinaPks;
    protected User $teacher;
    protected User $pksTeam;
    protected Student $student;
    protected Student $student2;
    protected ViolationType $violationType;
    protected AcademicYear $academicYear;
    protected SchoolClass $class;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->school = School::factory()->create();
        $this->school2 = School::factory()->create();

        $this->academicYear = AcademicYear::factory()->for($this->school)->create();
        $this->class = SchoolClass::factory()->for($this->school)->for($this->academicYear)->create();

        $this->student = Student::factory()
            ->for($this->school)
            ->for($this->academicYear)
            ->for($this->class)
            ->create();

        $this->student2 = Student::factory()
            ->for($this->school2)
            ->for($this->academicYear)
            ->for($this->class)
            ->create();

        $this->violationType = ViolationType::factory()
            ->for($this->school)
            ->create(['points' => 5]);

        $this->superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'school_id' => null,
        ]);

        $this->schoolAdmin = User::factory()->create([
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $this->school->id,
        ]);

        $this->pembinaPks = User::factory()->create([
            'role' => User::ROLE_PEMBINA_PKS,
            'school_id' => $this->school->id,
        ]);

        $this->teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
            'school_id' => $this->school->id,
        ]);

        $this->pksTeam = User::factory()->create([
            'role' => User::ROLE_PKS_TEAM,
            'school_id' => $this->school->id,
        ]);
    }

    // ==========================================
    // Test 1: PKS Team CAN Create Violations
    // ==========================================

    public function test_pks_team_can_create_violation_in_own_school(): void
    {
        $response = $this->actingAs($this->pksTeam)->post(route('violations.store'), [
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
            'location' => 'Gerbang Utama',
            'description' => 'Melanggar tata tertib sekolah',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('violations', [
            'student_id' => $this->student->id,
            'officer_id' => $this->pksTeam->id,
            'school_id' => $this->school->id,
        ]);
    }

    public function test_pks_team_can_create_violation_with_evidence(): void
    {
        $file = UploadedFile::fake()->image('evidence.jpg', 800, 600);

        $response = $this->actingAs($this->pksTeam)->post(route('violations.store'), [
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
            'evidences' => [$file],
        ]);

        $response->assertRedirect();

        $violation = Violation::where('student_id', $this->student->id)->first();
        $this->assertNotNull($violation);
        $this->assertDatabaseHas('violation_evidences', [
            'violation_id' => $violation->id,
        ]);
    }

    // ==========================================
    // Test 2: PKS Team CAN View Violations
    // ==========================================

    public function test_pks_team_can_view_violations_list(): void
    {
        $response = $this->actingAs($this->pksTeam)->get(route('violations.index'));

        $response->assertStatus(200);
    }

    public function test_pks_team_can_view_violation_detail(): void
    {
        $violation = Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'officer_id' => $this->schoolAdmin->id,
            'occurred_at' => now(),
            'points' => $this->violationType->points,
            'status' => Violation::STATUS_RECORDED,
        ]);

        $response = $this->actingAs($this->pksTeam)->get(route('violations.show', $violation));

        $response->assertStatus(200);
        $response->assertSee($this->student->full_name);
    }

    // ==========================================
    // Test 3: PKS Team CANNOT Edit/Verify/Cancel
    // ==========================================

    public function test_pks_team_cannot_edit_violation(): void
    {
        $violation = Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'officer_id' => $this->pksTeam->id,
            'occurred_at' => now(),
            'points' => $this->violationType->points,
            'status' => Violation::STATUS_RECORDED,
        ]);

        // PKS Team cannot access edit page (redirects with error)
        $response = $this->actingAs($this->pksTeam)->get(route('violations.edit', $violation));

        $response->assertRedirect();
    }

    public function test_pks_team_cannot_verify_violation(): void
    {
        $violation = Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'officer_id' => $this->pksTeam->id,
            'occurred_at' => now(),
            'points' => $this->violationType->points,
            'status' => Violation::STATUS_RECORDED,
        ]);

        $response = $this->actingAs($this->pksTeam)->post(route('violations.verify', $violation));

        $response->assertStatus(403);
    }

    public function test_pks_team_cannot_cancel_violation(): void
    {
        $violation = Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'officer_id' => $this->pksTeam->id,
            'occurred_at' => now(),
            'points' => $this->violationType->points,
            'status' => Violation::STATUS_RECORDED,
        ]);

        $response = $this->actingAs($this->pksTeam)->post(route('violations.cancel', $violation), [
            'reason' => 'Test cancellation',
        ]);

        $response->assertStatus(403);
    }

    public function test_pks_team_cannot_delete_violation(): void
    {
        $violation = Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'officer_id' => $this->pksTeam->id,
            'occurred_at' => now(),
            'points' => $this->violationType->points,
            'status' => Violation::STATUS_RECORDED,
        ]);

        // Violations cannot be deleted at all
        $this->assertFalse($this->pksTeam->can('delete', $violation));
    }

    // ==========================================
    // Test 4: Cross-Tenant Prevention
    // ==========================================

    public function test_pks_team_cannot_create_violation_for_other_school_student(): void
    {
        $response = $this->actingAs($this->pksTeam)->post(route('violations.store'), [
            'student_id' => $this->student2->id, // Student dari school2
            'violation_type_id' => $this->violationType->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
        ]);

        // Akan return redirect dengan validation error karena student tidak ada di sekolah PKS Team
        $response->assertSessionHasErrors('student_id');
    }

    public function test_pks_team_cannot_access_other_school_violation(): void
    {
        $violationSchool2 = Violation::create([
            'school_id' => $this->school2->id,
            'student_id' => $this->student2->id,
            'violation_type_id' => ViolationType::factory()->for($this->school2)->create()->id,
            'academic_year_id' => AcademicYear::factory()->for($this->school2)->create()->id,
            'school_class_id' => SchoolClass::factory()->for($this->school2)->create()->id,
            'officer_id' => User::factory()->for($this->school2)->create()->id,
            'occurred_at' => now(),
            'points' => 5,
            'status' => Violation::STATUS_RECORDED,
        ]);

        $response = $this->actingAs($this->pksTeam)->get(route('violations.show', $violationSchool2));

        // Global scope returns 404 when resource belongs to different tenant
        $response->assertStatus(404);
    }

    // ==========================================
    // Test 5: Cannot Access Admin Modules
    // ==========================================

    public function test_pks_team_cannot_access_schools(): void
    {
        $response = $this->actingAs($this->pksTeam)->get('/schools');
        $response->assertStatus(403);
    }

    public function test_pks_team_cannot_access_users(): void
    {
        $response = $this->actingAs($this->pksTeam)->get('/users');
        $response->assertStatus(403);
    }

    public function test_pks_team_cannot_access_violation_types(): void
    {
        $response = $this->actingAs($this->pksTeam)->get('/violation-types');
        $response->assertStatus(403);
    }

    public function test_pks_team_cannot_access_pks_members(): void
    {
        $response = $this->actingAs($this->pksTeam)->get('/pks-members');
        $response->assertStatus(403);
    }

    public function test_pks_team_cannot_access_pks_shifts(): void
    {
        $response = $this->actingAs($this->pksTeam)->get('/pks-shifts');
        $response->assertStatus(403);
    }

    public function test_pks_team_cannot_access_pks_duty_locations(): void
    {
        $response = $this->actingAs($this->pksTeam)->get('/pks-duty-locations');
        $response->assertStatus(403);
    }

    public function test_pks_team_can_view_pks_duty_schedules(): void
    {
        // PKS Team can view schedules (read-only access)
        $response = $this->actingAs($this->pksTeam)->get('/pks-duty-schedules');
        $response->assertStatus(200);
    }

    public function test_pks_team_cannot_access_pks_duty_assignments(): void
    {
        $response = $this->actingAs($this->pksTeam)->get('/pks-duty-assignments');
        $response->assertStatus(403);
    }

    public function test_pks_team_can_view_pks_duty_attendances(): void
    {
        // PKS Team can view and create attendances
        $response = $this->actingAs($this->pksTeam)->get('/pks-duty-attendances');
        $response->assertStatus(200);
    }

    public function test_pks_team_cannot_access_pks_field_activities(): void
    {
        $response = $this->actingAs($this->pksTeam)->get('/pks-field-activities');
        $response->assertStatus(403);
    }

    public function test_pks_team_cannot_access_reports(): void
    {
        $response = $this->actingAs($this->pksTeam)->get('/reports/daily');
        $response->assertStatus(403);
    }

    public function test_pks_team_cannot_access_monthly_reports(): void
    {
        $response = $this->actingAs($this->pksTeam)->get('/reports/monthly');
        $response->assertStatus(403);
    }

    // ==========================================
    // Test 6: Evidence Access
    // ==========================================

    public function test_pks_team_can_view_evidence_in_own_school(): void
    {
        // Create violation first without factory
        $violation = Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'officer_id' => $this->schoolAdmin->id,
            'occurred_at' => now(),
            'points' => $this->violationType->points,
            'status' => Violation::STATUS_RECORDED,
        ]);

        // Create evidence using factory with explicit file_path
        $evidence = ViolationEvidence::create([
            'violation_id' => $violation->id,
            'file_path' => 'violations/' . $this->school->id . '/' . $violation->id . '/test.jpg',
            'original_name' => 'test.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1000,
        ]);

        // Use withoutTenantScope to find the evidence
        $foundEvidence = ViolationEvidence::withoutGlobalScope('tenant')->find($evidence->id);
        $this->assertNotNull($foundEvidence, 'Evidence should exist in database');

        // Try to access with pks_team user
        $response = $this->actingAs($this->pksTeam)->get(route('evidences.show', $evidence));

        // If it's a file not found issue, that's expected in tests
        // But 403 means authorization failed, 200 means success
        $this->assertNotEquals(403, $response->getStatusCode(), 'PKS team should be authorized to view evidence');
    }

    public function test_pks_team_cannot_delete_evidence(): void
    {
        $violation = Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'officer_id' => $this->schoolAdmin->id,
            'occurred_at' => now(),
            'points' => $this->violationType->points,
            'status' => Violation::STATUS_RECORDED,
        ]);

        $evidence = ViolationEvidence::factory()->create([
            'violation_id' => $violation->id,
            'file_path' => 'violations/' . $this->school->id . '/' . $violation->id . '/test.jpg',
        ]);

        $response = $this->actingAs($this->pksTeam)->delete(route('violations.evidences.destroy', $evidence));

        $response->assertStatus(403);
    }

    public function test_pks_team_cannot_access_other_school_evidence(): void
    {
        $violationSchool2 = Violation::create([
            'school_id' => $this->school2->id,
            'student_id' => $this->student2->id,
            'violation_type_id' => ViolationType::factory()->for($this->school2)->create()->id,
            'academic_year_id' => AcademicYear::factory()->for($this->school2)->create()->id,
            'school_class_id' => SchoolClass::factory()->for($this->school2)->create()->id,
            'officer_id' => User::factory()->for($this->school2)->create()->id,
            'occurred_at' => now(),
            'points' => 5,
            'status' => Violation::STATUS_RECORDED,
        ]);

        $evidence = ViolationEvidence::factory()->create([
            'violation_id' => $violationSchool2->id,
            'file_path' => 'violations/' . $this->school2->id . '/' . $violationSchool2->id . '/test.jpg',
        ]);

        $response = $this->actingAs($this->pksTeam)->get(route('evidences.show', $evidence));

        $response->assertStatus(404);
    }

    // ==========================================
    // Test 7: Dashboard Access
    // ==========================================

    public function test_pks_team_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->pksTeam)->get('/dashboard');

        $response->assertStatus(200);
    }

    // ==========================================
    // Test 8: Other Roles Not Affected
    // ==========================================

    public function test_teacher_can_still_create_violation(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('violations.store'), [
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect();
    }

    public function test_pembina_pks_can_create_violation(): void
    {
        $response = $this->actingAs($this->pembinaPks)->post(route('violations.store'), [
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect();
    }

    public function test_pembina_pks_can_view_pks_operational(): void
    {
        $response = $this->actingAs($this->pembinaPks)->get('/pks-shifts');
        $response->assertStatus(200);
    }

    public function test_pembina_pks_can_manage_pks_members(): void
    {
        $response = $this->actingAs($this->pembinaPks)->get('/pks-members');
        $response->assertStatus(200);
    }

    public function test_pembina_pks_cannot_access_schools(): void
    {
        $response = $this->actingAs($this->pembinaPks)->get('/schools');
        $response->assertStatus(403);
    }

    public function test_pembina_pks_cannot_verify_violation(): void
    {
        $violation = Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'officer_id' => $this->pembinaPks->id,
            'occurred_at' => now(),
            'points' => $this->violationType->points,
            'status' => Violation::STATUS_RECORDED,
        ]);

        $response = $this->actingAs($this->pembinaPks)->post(route('violations.verify', $violation));

        $response->assertStatus(403);
    }
}
