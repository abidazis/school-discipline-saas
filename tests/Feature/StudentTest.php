<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private School $school;
    private AcademicYear $academicYear;
    private Department $department;
    private SchoolClass $schoolClass;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->school = School::factory()->create();
        $this->academicYear = AcademicYear::factory()->for($this->school)->create();
        $this->department = Department::factory()->for($this->school)->create();
        $this->schoolClass = SchoolClass::factory()->for($this->school)->for($this->academicYear)->for($this->department)->create();
    }

    public function test_can_list_students(): void
    {
        Student::factory()->count(5)->for($this->school)->for($this->academicYear)->for($this->schoolClass)->create();

        $response = $this->actingAs($this->superAdmin)->get('/students');

        $response->assertStatus(200);
        $response->assertViewHas('students');
    }

    public function test_can_create_student(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();

        $response = $this->actingAs($schoolAdmin)->post('/students', [
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->schoolClass->id,
            'nis' => '12345',
            'nisn' => '1234567890',
            'full_name' => 'John Doe',
            'gender' => 'male',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-01-15',
            'address' => 'Jl. Sudirman No. 1',
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        $response->assertRedirect('/students');
        $this->assertDatabaseHas('students', [
            'nis' => '12345',
            'full_name' => 'John Doe',
        ]);
    }

    public function test_can_search_students_by_nis(): void
    {
        Student::factory()->for($this->school)->for($this->academicYear)->for($this->schoolClass)->create(['nis' => '11111', 'full_name' => 'Student A']);
        Student::factory()->for($this->school)->for($this->academicYear)->for($this->schoolClass)->create(['nis' => '22222', 'full_name' => 'Student B']);

        $response = $this->actingAs($this->superAdmin)->get('/students?search=11111');

        $response->assertSee('Student A');
        $response->assertDontSee('Student B');
    }

    public function test_can_search_students_by_nisn(): void
    {
        Student::factory()->for($this->school)->for($this->academicYear)->for($this->schoolClass)->create(['nisn' => '9999999999', 'full_name' => 'Student A']);
        Student::factory()->for($this->school)->for($this->academicYear)->for($this->schoolClass)->create(['nisn' => '8888888888', 'full_name' => 'Student B']);

        $response = $this->actingAs($this->superAdmin)->get('/students?search=9999999999');

        $response->assertSee('Student A');
        $response->assertDontSee('Student B');
    }

    public function test_can_search_students_by_name(): void
    {
        Student::factory()->for($this->school)->for($this->academicYear)->for($this->schoolClass)->create(['full_name' => 'Ahmad Rizki']);
        Student::factory()->for($this->school)->for($this->academicYear)->for($this->schoolClass)->create(['full_name' => 'Budi Santoso']);

        $response = $this->actingAs($this->superAdmin)->get('/students?search=Ahmad');

        $response->assertSee('Ahmad Rizki');
        $response->assertDontSee('Budi Santoso');
    }

    public function test_can_filter_students_by_academic_year(): void
    {
        $year1 = AcademicYear::factory()->for($this->school)->create([
            'name' => '2099/2100',
            'start_date' => '2099-07-01',
            'end_date' => '2100-06-30',
            'is_active' => true,
        ]);
        $year2 = AcademicYear::factory()->for($this->school)->create([
            'name' => '2098/2099',
            'start_date' => '2098-07-01',
            'end_date' => '2099-06-30',
            'is_active' => false,
        ]);

        $class1 = SchoolClass::factory()->for($this->school)->for($year1)->for($this->department)->create();
        $class2 = SchoolClass::factory()->for($this->school)->for($year2)->for($this->department)->create();

        Student::factory()->for($this->school)->for($year1)->for($class1)->create(['full_name' => 'Student Year1']);
        Student::factory()->for($this->school)->for($year2)->for($class2)->create(['full_name' => 'Student Year2']);

        $response = $this->actingAs($this->superAdmin)->get("/students?academic_year_id={$year1->id}");

        $response->assertSee('Student Year1');
        $response->assertDontSee('Student Year2');
    }

    public function test_can_filter_students_by_class(): void
    {
        $class1 = SchoolClass::factory()->for($this->school)->for($this->academicYear)->for($this->department)->create(['name' => '1']);
        $class2 = SchoolClass::factory()->for($this->school)->for($this->academicYear)->for($this->department)->create(['name' => '2']);

        Student::factory()->for($this->school)->for($this->academicYear)->for($class1)->create(['full_name' => 'Student 1']);
        Student::factory()->for($this->school)->for($this->academicYear)->for($class2)->create(['full_name' => 'Student 2']);

        $response = $this->actingAs($this->superAdmin)->get("/students?school_class_id={$class1->id}");

        $response->assertSee('Student 1');
        $response->assertDontSee('Student 2');
    }

    public function test_can_filter_students_by_status(): void
    {
        Student::factory()->for($this->school)->for($this->academicYear)->for($this->schoolClass)->create(['full_name' => 'Active Student', 'status' => 'active']);
        Student::factory()->for($this->school)->for($this->academicYear)->for($this->schoolClass)->create(['full_name' => 'Graduated Student', 'status' => 'graduated']);

        $response = $this->actingAs($this->superAdmin)->get('/students?status=active');

        $response->assertSee('Active Student');
        $response->assertDontSee('Graduated Student');
    }

    public function test_nis_must_be_unique_within_school(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();
        Student::factory()->for($this->school)->for($this->academicYear)->for($this->schoolClass)->create(['nis' => '12345']);

        $response = $this->actingAs($schoolAdmin)->post('/students', [
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->schoolClass->id,
            'nis' => '12345',
            'full_name' => 'Another Student',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('nis');
    }

    public function test_teacher_can_view_students_but_not_create(): void
    {
        $teacher = User::factory()->teacher()->forSchool($this->school)->create();

        $response = $this->actingAs($teacher)->get('/students');

        $response->assertStatus(200);

        $createResponse = $this->actingAs($teacher)->get('/students/create');
        $createResponse->assertStatus(403);
    }

    public function test_operator_can_create_and_edit_students(): void
    {
        $operator = User::factory()->operator()->forSchool($this->school)->create();

        // Can create
        $response = $this->actingAs($operator)->post('/students', [
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->schoolClass->id,
            'nis' => '12345',
            'full_name' => 'New Student',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $response->assertRedirect('/students');

        // Can edit
        $student = Student::factory()->for($this->school)->for($this->academicYear)->for($this->schoolClass)->create();
        $editResponse = $this->actingAs($operator)->get("/students/{$student->id}/edit");
        $editResponse->assertStatus(200);
    }

    public function test_tenant_isolation_for_students(): void
    {
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();

        $admin1 = User::factory()->schoolAdmin()->forSchool($school1)->create();
        $admin2 = User::factory()->schoolAdmin()->forSchool($school2)->create();

        $year1 = AcademicYear::factory()->for($school1)->create();
        $year2 = AcademicYear::factory()->for($school2)->create();

        $class1 = SchoolClass::factory()->for($school1)->for($year1)->create();
        $class2 = SchoolClass::factory()->for($school2)->for($year2)->create();

        Student::factory()->for($school1)->for($year1)->for($class1)->create(['full_name' => 'School 1 Student', 'nis' => '11111']);
        Student::factory()->for($school2)->for($year2)->for($class2)->create(['full_name' => 'School 2 Student', 'nis' => '22222']);

        // Admin 1 should only see School 1's students
        $response = $this->actingAs($admin1)->get('/students');
        $response->assertSee('School 1 Student');
        $response->assertDontSee('School 2 Student');

        // Admin 2 should only see School 2's students
        $response = $this->actingAs($admin2)->get('/students');
        $response->assertSee('School 2 Student');
        $response->assertDontSee('School 1 Student');
    }

    public function test_cannot_use_academic_year_from_different_school(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();
        $otherSchool = School::factory()->create();
        $otherYear = AcademicYear::factory()->for($otherSchool)->create();

        $response = $this->actingAs($schoolAdmin)->post('/students', [
            'academic_year_id' => $otherYear->id,
            'school_class_id' => $this->schoolClass->id,
            'nis' => '12345',
            'full_name' => 'Test Student',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('academic_year_id');
    }

    public function test_cannot_use_class_from_different_school(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();
        $otherSchool = School::factory()->create();
        $otherYear = AcademicYear::factory()->for($otherSchool)->create();
        $otherClass = SchoolClass::factory()->for($otherSchool)->for($otherYear)->create();

        $response = $this->actingAs($schoolAdmin)->post('/students', [
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $otherClass->id,
            'nis' => '12345',
            'full_name' => 'Test Student',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('school_class_id');
    }

    public function test_can_view_student_detail(): void
    {
        $student = Student::factory()
            ->for($this->school)
            ->for($this->academicYear)
            ->for($this->schoolClass)
            ->create();

        $response = $this->actingAs($this->superAdmin)->get("/students/{$student->id}");

        $response->assertStatus(200);
        $response->assertSee($student->full_name);
    }

    public function test_can_update_student(): void
    {
        $operator = User::factory()->operator()->forSchool($this->school)->create();
        $student = Student::factory()
            ->for($this->school)
            ->for($this->academicYear)
            ->for($this->schoolClass)
            ->create();

        $response = $this->actingAs($operator)->patch("/students/{$student->id}", [
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->schoolClass->id,
            'nis' => $student->nis,
            'full_name' => 'Updated Name',
            'gender' => $student->gender,
            'status' => 'graduated',
        ]);

        $response->assertRedirect("/students/{$student->id}");
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'full_name' => 'Updated Name',
            'status' => 'graduated',
        ]);
    }

    public function test_can_delete_student(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();
        $student = Student::factory()
            ->for($this->school)
            ->for($this->academicYear)
            ->for($this->schoolClass)
            ->create();

        $response = $this->actingAs($schoolAdmin)->delete("/students/{$student->id}");

        $response->assertRedirect('/students');
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_gender_validation(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();

        $response = $this->actingAs($schoolAdmin)->post('/students', [
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->schoolClass->id,
            'nis' => '12345',
            'full_name' => 'Test Student',
            'gender' => 'invalid_gender',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('gender');
    }

    public function test_status_validation(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();

        $response = $this->actingAs($schoolAdmin)->post('/students', [
            'academic_year_id' => $this->academicYear->id,
            'school_class_id' => $this->schoolClass->id,
            'nis' => '12345',
            'full_name' => 'Test Student',
            'gender' => 'male',
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('status');
    }
}
