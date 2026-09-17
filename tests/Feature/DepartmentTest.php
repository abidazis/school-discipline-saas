<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private School $school;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->school = School::factory()->create();
    }

    public function test_can_list_departments(): void
    {
        Department::factory()->count(3)->for($this->school)->create();

        $response = $this->actingAs($this->superAdmin)->get('/departments');

        $response->assertStatus(200);
        $response->assertViewHas('departments');
    }

    public function test_can_create_department(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();

        $response = $this->actingAs($schoolAdmin)->post('/departments', [
            'name' => 'Teknik Komputer dan Jaringan',
            'code' => 'TKJ',
            'is_active' => true,
        ]);

        $response->assertRedirect('/departments');
        $this->assertDatabaseHas('departments', [
            'name' => 'Teknik Komputer dan Jaringan',
            'code' => 'TKJ',
        ]);
    }

    public function test_department_code_must_be_unique_within_school(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();

        Department::factory()->for($this->school)->create(['code' => 'TKJ']);

        $response = $this->actingAs($schoolAdmin)->post('/departments', [
            'name' => 'Teknik Komputer Lain',
            'code' => 'TKJ',
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_teacher_can_view_but_not_create_department(): void
    {
        $teacher = User::factory()->teacher()->forSchool($this->school)->create();

        $response = $this->actingAs($teacher)->get('/departments');

        $response->assertStatus(200);

        $createResponse = $this->actingAs($teacher)->get('/departments/create');
        $createResponse->assertStatus(403);
    }

    public function test_tenant_isolation_for_departments(): void
    {
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();

        $admin1 = User::factory()->schoolAdmin()->forSchool($school1)->create();
        $admin2 = User::factory()->schoolAdmin()->forSchool($school2)->create();

        Department::factory()->for($school1)->create(['code' => 'TKJ', 'name' => 'School 1 Dept']);
        Department::factory()->for($school2)->create(['code' => 'RPL', 'name' => 'School 2 Dept']);

        // Admin 1 should only see School 1's departments
        $response = $this->actingAs($admin1)->get('/departments');
        $response->assertSee('School 1 Dept');
        $response->assertDontSee('School 2 Dept');

        // Admin 2 should only see School 2's departments
        $response = $this->actingAs($admin2)->get('/departments');
        $response->assertSee('School 2 Dept');
        $response->assertDontSee('School 1 Dept');
    }

    public function test_cannot_delete_department_with_classes(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();
        $dept = Department::factory()->for($this->school)->create();

        // Create a class for this department
        $year = \App\Models\AcademicYear::factory()->for($this->school)->create();
        \App\Models\SchoolClass::factory()->for($this->school)->for($year)->for($dept)->create();

        $response = $this->actingAs($schoolAdmin)->delete("/departments/{$dept->id}");

        $response->assertRedirect("/departments/{$dept->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('departments', ['id' => $dept->id]);
    }

    public function test_can_deactivate_department(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();
        $dept = Department::factory()->for($this->school)->create(['is_active' => true]);

        $this->actingAs($schoolAdmin)->patch("/departments/{$dept->id}", [
            'name' => $dept->name,
            'code' => $dept->code,
            'is_active' => false,
        ]);

        $dept->refresh();
        $this->assertFalse($dept->is_active);
    }
}
