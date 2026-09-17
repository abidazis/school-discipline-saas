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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ViolationTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected School $school2;
    protected User $superAdmin;
    protected User $schoolAdmin;
    protected User $operator;
    protected User $teacher;
    protected Student $student;
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
    // CRITICAL: Point Snapshot Test
    // ==========================================

    public function test_point_snapshot_preserved_after_violation_type_update(): void
    {
        // 1. Create violation type with points = 5
        $type = ViolationType::factory()->for($this->school)->create(['points' => 5]);

        // 2. Create violation directly with explicit fields
        $violation = Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'violation_type_id' => $type->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'officer_id' => $this->schoolAdmin->id,
            'occurred_at' => now(),
            'points' => $type->points, // Snapshot the points
            'status' => Violation::STATUS_RECORDED,
        ]);

        // 3. Assert violation.points = 5
        $this->assertEquals(5, $violation->points);

        // 4. Update violation type points = 10
        $type->update(['points' => 10]);

        // 5. Assert violation.points still = 5
        $violation->refresh();
        $this->assertEquals(5, $violation->points);

        // 6. Assert student total active points = 5
        $this->assertEquals(5, $this->student->active_points);
    }

    // ==========================================
    // CRITICAL: Class Snapshot Test
    // ==========================================

    public function test_class_snapshot_preserved_after_student_class_change(): void
    {
        // 1. Student belongs to Class A
        $classA = SchoolClass::factory()
            ->for($this->school)
            ->for($this->academicYear)
            ->create(['name' => 'A']);

        $student = Student::factory()
            ->for($this->school)
            ->for($this->academicYear)
            ->for($classA)
            ->create();

        // 2. Create violation
        $violation = Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'violation_type_id' => $this->violationType->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $classA->id,
            'officer_id' => $this->schoolAdmin->id,
            'occurred_at' => now(),
            'points' => $this->violationType->points,
            'status' => Violation::STATUS_RECORDED,
        ]);

        // 3. Assert violation.school_class_id = Class A
        $this->assertEquals($classA->id, $violation->school_class_id);

        // 4. Move student to Class B
        $classB = SchoolClass::factory()
            ->for($this->school)
            ->for($this->academicYear)
            ->create(['name' => 'B']);

        $student->update(['school_class_id' => $classB->id]);

        // 5. Assert violation.school_class_id remains Class A
        $violation->refresh();
        $this->assertEquals($classA->id, $violation->school_class_id);
    }

    // ==========================================
    // CRITICAL: Cancelled Points Test
    // ==========================================

    public function test_cancelled_violation_excluded_from_total_points(): void
    {
        // Create Violation A = 5 points
        $violationA = Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'officer_id' => $this->schoolAdmin->id,
            'occurred_at' => now(),
            'points' => 5,
            'status' => Violation::STATUS_RECORDED,
        ]);

        // Create Violation B = 10 points
        $typeB = ViolationType::factory()->for($this->school)->create(['points' => 10]);
        $violationB = Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'violation_type_id' => $typeB->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'officer_id' => $this->schoolAdmin->id,
            'occurred_at' => now(),
            'points' => 10,
            'status' => Violation::STATUS_RECORDED,
        ]);

        // Assert total = 15
        $this->assertEquals(15, $this->student->active_points);

        // Cancel Violation B
        $violationB->cancel('Test cancellation');

        // Assert total active = 5
        $this->assertEquals(5, $this->student->active_points);

        // Assert both violations remain in history
        $this->assertDatabaseHas('violations', ['id' => $violationA->id, 'status' => 'recorded']);
        $this->assertDatabaseHas('violations', ['id' => $violationB->id, 'status' => 'cancelled']);
    }

    // ==========================================
    // Cross-Tenant Validation Tests
    // ==========================================

    public function test_cross_tenant_violation_rejected_student_different_school(): void
    {
        $studentSchool2 = Student::factory()->for($this->school2)->create();

        $response = $this->actingAs($this->schoolAdmin)->post(route('violations.store'), [
            'student_id' => $studentSchool2->id,
            'violation_type_id' => $this->violationType->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('student_id');
    }

    public function test_cross_tenant_violation_rejected_violation_type_different_school(): void
    {
        $typeSchool2 = ViolationType::factory()->for($this->school2)->create();

        $response = $this->actingAs($this->schoolAdmin)->post(route('violations.store'), [
            'student_id' => $this->student->id,
            'violation_type_id' => $typeSchool2->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('violation_type_id');
    }

    public function test_cross_tenant_violation_rejected_academic_year_different_school(): void
    {
        $yearSchool2 = AcademicYear::factory()->for($this->school2)->create();

        $studentSchool2 = Student::factory()->for($this->school2)->for($yearSchool2)->create();
        $typeSchool2 = ViolationType::factory()->for($this->school2)->create();

        $response = $this->actingAs($this->schoolAdmin)->post(route('violations.store'), [
            'student_id' => $studentSchool2->id,
            'violation_type_id' => $typeSchool2->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('student_id');
    }

    // ==========================================
    // Basic CRUD Tests
    // ==========================================

    public function test_can_record_violation(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('violations.store'), [
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
            'location' => 'Gerbang Depan',
            'description' => 'Test description',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('violations', [
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'school_id' => $this->school->id,
            'points' => $this->violationType->points,
            'status' => 'recorded',
        ]);
    }

    public function test_teacher_can_record_violation(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('violations.store'), [
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('violations', [
            'student_id' => $this->student->id,
            'officer_id' => $this->teacher->id,
        ]);
    }

    public function test_operator_can_view_violations(): void
    {
        Violation::create([
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

        $response = $this->actingAs($this->operator)->get(route('violations.index'));

        $response->assertStatus(200);
    }

    public function test_operator_cannot_verify_violation(): void
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

        $response = $this->actingAs($this->operator)->post(route('violations.verify', $violation));

        $response->assertStatus(403);
    }

    public function test_operator_cannot_cancel_violation(): void
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

        $response = $this->actingAs($this->operator)->post(route('violations.cancel', $violation), [
            'reason' => 'Test',
        ]);

        $response->assertStatus(403);
    }

    public function test_school_admin_can_verify_violation(): void
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

        $response = $this->actingAs($this->schoolAdmin)->post(route('violations.verify', $violation));

        $response->assertRedirect();
        $this->assertDatabaseHas('violations', [
            'id' => $violation->id,
            'status' => 'verified',
        ]);
    }

    public function test_school_admin_can_cancel_violation(): void
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

        $response = $this->actingAs($this->schoolAdmin)->post(route('violations.cancel', $violation), [
            'reason' => 'Kesalahan input',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('violations', [
            'id' => $violation->id,
            'status' => 'cancelled',
            'cancelled_reason' => 'Kesalahan input',
        ]);
    }

    // ==========================================
    // Search and Filter Tests
    // ==========================================

    public function test_can_search_violations_by_student_name(): void
    {
        $student = Student::factory()
            ->for($this->school)
            ->for($this->academicYear)
            ->for($this->class)
            ->create(['full_name' => 'Ahmad Rizki']);

        Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'violation_type_id' => $this->violationType->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'officer_id' => $this->schoolAdmin->id,
            'occurred_at' => now(),
            'points' => $this->violationType->points,
            'status' => Violation::STATUS_RECORDED,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('violations.index', ['search' => 'Ahmad']));

        $response->assertStatus(200);
        $response->assertSee('Ahmad Rizki');
    }

    public function test_can_search_violations_by_nis(): void
    {
        $student = Student::factory()
            ->for($this->school)
            ->for($this->academicYear)
            ->for($this->class)
            ->create(['nis' => '12345']);

        Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'violation_type_id' => $this->violationType->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'officer_id' => $this->schoolAdmin->id,
            'occurred_at' => now(),
            'points' => $this->violationType->points,
            'status' => Violation::STATUS_RECORDED,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('violations.index', ['search' => '12345']));

        $response->assertStatus(200);
        $response->assertSee('12345');
    }

    public function test_can_filter_violations_by_status(): void
    {
        Violation::create([
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

        Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->class->id,
            'officer_id' => $this->schoolAdmin->id,
            'occurred_at' => now(),
            'points' => $this->violationType->points,
            'status' => Violation::STATUS_VERIFIED,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('violations.index', ['status' => 'recorded']));

        $response->assertStatus(200);
    }

    // ==========================================
    // Violation Detail Tests
    // ==========================================

    public function test_can_view_violation_detail(): void
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

        $response = $this->actingAs($this->schoolAdmin)->get(route('violations.show', $violation));

        $response->assertStatus(200);
        $response->assertSee($this->student->full_name);
        $response->assertSee($this->violationType->name);
    }

    public function test_verified_violation_cannot_be_edited(): void
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
            'status' => Violation::STATUS_VERIFIED,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('violations.edit', $violation));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_cancelled_violation_cannot_be_edited(): void
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
            'status' => Violation::STATUS_CANCELLED,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('violations.edit', $violation));

        $response->assertRedirect();
    }

    public function test_can_edit_recorded_violation(): void
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

        $response = $this->actingAs($this->schoolAdmin)->get(route('violations.edit', $violation));

        $response->assertStatus(200);
    }

    // ==========================================
    // Evidence Upload Tests
    // ==========================================

    public function test_can_upload_jpeg_evidence(): void
    {
        $file = UploadedFile::fake()->image('evidence.jpg', 800, 600);

        $response = $this->actingAs($this->schoolAdmin)->post(route('violations.store'), [
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
            'mime_type' => 'image/jpeg',
        ]);
    }

    public function test_can_upload_png_evidence(): void
    {
        $file = UploadedFile::fake()->image('evidence.png', 800, 600);

        $response = $this->actingAs($this->schoolAdmin)->post(route('violations.store'), [
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
            'evidences' => [$file],
        ]);

        $response->assertRedirect();

        $violation = Violation::where('student_id', $this->student->id)->first();
        $this->assertDatabaseHas('violation_evidences', [
            'violation_id' => $violation->id,
            'mime_type' => 'image/png',
        ]);
    }

    public function test_invalid_file_type_rejected(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->actingAs($this->schoolAdmin)->post(route('violations.store'), [
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
            'evidences' => [$file],
        ]);

        $response->assertSessionHasErrors('evidences.0');
    }

    public function test_oversized_file_rejected(): void
    {
        $file = UploadedFile::fake()->image('large.jpg')->size(6000); // 6MB

        $response = $this->actingAs($this->schoolAdmin)->post(route('violations.store'), [
            'student_id' => $this->student->id,
            'violation_type_id' => $this->violationType->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
            'evidences' => [$file],
        ]);

        $response->assertSessionHasErrors('evidences.0');
    }

    public function test_evidence_security_tenant_isolation(): void
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

        $evidence = ViolationEvidence::factory()->forViolation($violation)->create();

        $adminSchool2 = User::factory()->create([
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $this->school2->id,
        ]);

        // Admin from school2 should not access school1's evidence
        // Due to global scope on Violation, this returns 404
        $response = $this->actingAs($adminSchool2)->get(route('evidences.show', $evidence));

        $response->assertStatus(404);
    }

    public function test_deleting_evidence_does_not_delete_violation(): void
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

        $evidence = ViolationEvidence::factory()->forViolation($violation)->create();

        $this->actingAs($this->schoolAdmin)->delete(route('violations.evidences.destroy', $evidence));

        $this->assertDatabaseMissing('violation_evidences', ['id' => $evidence->id]);
        $this->assertDatabaseHas('violations', ['id' => $violation->id]);
    }

    // ==========================================
    // Academic Year Snapshot Test
    // ==========================================

    public function test_academic_year_snapshot_preserved(): void
    {
        // Use completely different years to avoid unique constraint conflicts
        $year1 = AcademicYear::factory()->for($this->school)->create([
            'name' => '2020/2021',
            'start_date' => '2020-07-01',
            'end_date' => '2021-06-30',
            'is_active' => false,
        ]);
        $year2 = AcademicYear::factory()->for($this->school)->create([
            'name' => '2030/2031',
            'start_date' => '2030-07-01',
            'end_date' => '2031-06-30',
            'is_active' => true,
        ]);

        $class1 = SchoolClass::factory()->for($this->school)->for($year1)->create();

        $student = Student::factory()
            ->for($this->school)
            ->for($year1)
            ->for($class1)
            ->create();

        $violation = Violation::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'violation_type_id' => $this->violationType->id,
            'academic_year_id' => $year1->id,
            'school_class_id' => $class1->id,
            'officer_id' => $this->schoolAdmin->id,
            'occurred_at' => now(),
            'points' => $this->violationType->points,
            'status' => Violation::STATUS_RECORDED,
        ]);

        // Update student's academic year
        $class2 = SchoolClass::factory()->for($this->school)->for($year2)->create();
        $student->update([
            'academic_year_id' => $year2->id,
            'school_class_id' => $class2->id,
        ]);

        // Violation should still reference year1
        $violation->refresh();
        $this->assertEquals($year1->id, $violation->academic_year_id);
    }

    // ==========================================
    // Tenant Isolation Tests
    // ==========================================

    public function test_tenant_a_cannot_access_violation_type_b(): void
    {
        $typeSchool2 = ViolationType::factory()->for($this->school2)->create();

        $response = $this->actingAs($this->schoolAdmin)->get(route('violation-types.show', $typeSchool2));

        // Global scope returns 404 when resource belongs to different tenant
        $response->assertStatus(404);
    }

    public function test_tenant_a_cannot_access_violation_b(): void
    {
        $violationSchool2 = Violation::create([
            'school_id' => $this->school2->id,
            'student_id' => Student::factory()->for($this->school2)->create()->id,
            'violation_type_id' => ViolationType::factory()->for($this->school2)->create()->id,
            'academic_year_id' => AcademicYear::factory()->for($this->school2)->create()->id,
            'school_class_id' => SchoolClass::factory()->for($this->school2)->create()->id,
            'officer_id' => User::factory()->for($this->school2)->create()->id,
            'occurred_at' => now(),
            'points' => 5,
            'status' => Violation::STATUS_RECORDED,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('violations.show', $violationSchool2));

        // Global scope returns 404 when resource belongs to different tenant
        $response->assertStatus(404);
    }
}
