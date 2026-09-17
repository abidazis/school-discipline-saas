<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolClassTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private School $school;
    private AcademicYear $academicYear;
    private Department $department;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->school = School::factory()->create();
        $this->academicYear = AcademicYear::factory()->for($this->school)->create();
        $this->department = Department::factory()->for($this->school)->create();
    }

    public function test_can_list_classes(): void
    {
        SchoolClass::factory()->count(3)->for($this->school)->for($this->academicYear)->create();

        $response = $this->actingAs($this->superAdmin)->get('/classes');

        $response->assertStatus(200);
        $response->assertViewHas('schoolClasses');
    }

    public function test_can_create_class(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();

        $response = $this->actingAs($schoolAdmin)->post('/classes', [
            'academic_year_id' => $this->academicYear->id,
            'department_id' => $this->department->id,
            'name' => '1',
            'grade_level' => 'X',
            'is_active' => true,
        ]);

        $response->assertRedirect('/classes');
        $this->assertDatabaseHas('school_classes', [
            'name' => '1',
            'grade_level' => 'X',
            'school_id' => $this->school->id,
        ]);
    }

    public function test_can_create_class_without_department(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();

        $response = $this->actingAs($schoolAdmin)->post('/classes', [
            'academic_year_id' => $this->academicYear->id,
            'department_id' => '',
            'name' => 'A',
            'grade_level' => 'VII',
            'is_active' => true,
        ]);

        $response->assertRedirect('/classes');
        $this->assertDatabaseHas('school_classes', [
            'name' => 'A',
            'grade_level' => 'VII',
            'department_id' => null,
        ]);
    }

    public function test_teacher_can_view_but_not_create_class(): void
    {
        $teacher = User::factory()->teacher()->forSchool($this->school)->create();

        $response = $this->actingAs($teacher)->get('/classes');

        $response->assertStatus(200);

        $createResponse = $this->actingAs($teacher)->get('/classes/create');
        $createResponse->assertStatus(403);
    }

    public function test_tenant_isolation_for_classes(): void
    {
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();

        $admin1 = User::factory()->schoolAdmin()->forSchool($school1)->create();
        $admin2 = User::factory()->schoolAdmin()->forSchool($school2)->create();

        $year1 = AcademicYear::factory()->for($school1)->create();
        $year2 = AcademicYear::factory()->for($school2)->create();

        SchoolClass::factory()->for($school1)->for($year1)->create(['name' => 'School 1 Class']);
        SchoolClass::factory()->for($school2)->for($year2)->create(['name' => 'School 2 Class']);

        // Admin 1 should only see School 1's classes
        $response = $this->actingAs($admin1)->get('/classes');
        $response->assertSee('School 1 Class');
        $response->assertDontSee('School 2 Class');

        // Admin 2 should only see School 2's classes
        $response = $this->actingAs($admin2)->get('/classes');
        $response->assertSee('School 2 Class');
        $response->assertDontSee('School 1 Class');
    }

    public function test_cannot_use_academic_year_from_different_school(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();
        $otherSchool = School::factory()->create();
        $otherYear = AcademicYear::factory()->for($otherSchool)->create();

        $response = $this->actingAs($schoolAdmin)->post('/classes', [
            'academic_year_id' => $otherYear->id,
            'department_id' => $this->department->id,
            'name' => '1',
            'grade_level' => 'X',
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('academic_year_id');
    }

    public function test_cannot_use_department_from_different_school(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();
        $otherSchool = School::factory()->create();
        $otherDept = Department::factory()->for($otherSchool)->create();

        $response = $this->actingAs($schoolAdmin)->post('/classes', [
            'academic_year_id' => $this->academicYear->id,
            'department_id' => $otherDept->id,
            'name' => '1',
            'grade_level' => 'X',
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('department_id');
    }

    public function test_cannot_delete_class_with_students(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();
        $class = SchoolClass::factory()->for($this->school)->for($this->academicYear)->create();

        // Create a student for this class
        \App\Models\Student::factory()->for($this->school)->for($this->academicYear)->for($class)->create();

        $response = $this->actingAs($schoolAdmin)->delete("/classes/{$class->id}");

        // Should redirect back and class should still exist
        $response->assertStatus(302);
        $this->assertDatabaseHas('school_classes', ['id' => $class->id]);
    }

    public function test_can_filter_classes_by_academic_year(): void
    {
        SchoolClass::factory()->for($this->school)->for($this->academicYear)->create(['name' => 'Current Year']);
        $oldYear = AcademicYear::factory()->for($this->school)->create([
            'name' => '2099/2100',
            'start_date' => '2099-07-01',
            'end_date' => '2100-06-30',
            'is_active' => false,
        ]);
        SchoolClass::factory()->for($this->school)->for($oldYear)->create(['name' => 'Old Year']);

        $response = $this->actingAs($this->superAdmin)->get("/classes?academic_year_id={$this->academicYear->id}");

        $response->assertSee('Current Year');
        $response->assertDontSee('Old Year');
    }

    public function test_can_filter_classes_by_department(): void
    {
        $dept2 = Department::factory()->for($this->school)->create();

        SchoolClass::factory()->for($this->school)->for($this->academicYear)->for($this->department)->create(['name' => 'TKJ Class']);
        SchoolClass::factory()->for($this->school)->for($this->academicYear)->for($dept2)->create(['name' => 'Other Class']);

        $response = $this->actingAs($this->superAdmin)->get("/classes?department_id={$this->department->id}");

        $response->assertSee('TKJ Class');
        $response->assertDontSee('Other Class');
    }
}
