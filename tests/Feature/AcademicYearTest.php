<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearTest extends TestCase
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

    public function test_can_list_academic_years(): void
    {
        AcademicYear::factory()->count(3)->for($this->school)->create();

        $response = $this->actingAs($this->superAdmin)->get('/academic-years');

        $response->assertStatus(200);
        $response->assertViewHas('academicYears');
    }

    public function test_can_create_academic_year(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();

        $response = $this->actingAs($schoolAdmin)->get('/academic-years/create');

        $response->assertStatus(200);
    }

    public function test_can_store_academic_year(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();

        $response = $this->actingAs($schoolAdmin)->post('/academic-years', [
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        $response->assertRedirect('/academic-years');
        $this->assertDatabaseHas('academic_years', [
            'name' => '2026/2027',
            'school_id' => $this->school->id,
        ]);
    }

    public function test_only_one_academic_year_can_be_active(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();

        // Create first active year
        $year1 = AcademicYear::factory()->for($this->school)->create(['is_active' => true]);

        // Create second year and set as active
        $this->actingAs($schoolAdmin)->post('/academic-years', [
            'name' => '2027/2028',
            'start_date' => '2027-07-01',
            'end_date' => '2028-06-30',
            'is_active' => true,
        ]);

        $year1->refresh();

        $this->assertFalse($year1->is_active);
        $this->assertTrue(AcademicYear::where('school_id', $this->school->id)->where('is_active', true)->count() === 1);
    }

    public function test_teacher_can_view_but_not_create_academic_year(): void
    {
        $teacher = User::factory()->teacher()->forSchool($this->school)->create();

        $response = $this->actingAs($teacher)->get('/academic-years');

        $response->assertStatus(200);

        $createResponse = $this->actingAs($teacher)->get('/academic-years/create');
        $createResponse->assertStatus(403);
    }

    public function test_tenant_isolation_for_academic_years(): void
    {
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();

        $admin1 = User::factory()->schoolAdmin()->forSchool($school1)->create();
        $admin2 = User::factory()->schoolAdmin()->forSchool($school2)->create();

        $year1 = AcademicYear::factory()->for($school1)->create(['name' => 'School 1 Year']);
        $year2 = AcademicYear::factory()->for($school2)->create(['name' => 'School 2 Year']);

        // Admin 1 should only see School 1's years
        $response = $this->actingAs($admin1)->get('/academic-years');
        $response->assertSee('School 1 Year');
        $response->assertDontSee('School 2 Year');

        // Admin 2 should only see School 2's years
        $response = $this->actingAs($admin2)->get('/academic-years');
        $response->assertSee('School 2 Year');
        $response->assertDontSee('School 1 Year');
    }

    public function test_cannot_delete_academic_year_with_classes(): void
    {
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($this->school)->create();
        $year = AcademicYear::factory()->for($this->school)->create();

        // Create a class for this year
        \App\Models\SchoolClass::factory()->for($this->school)->for($year)->create();

        $response = $this->actingAs($schoolAdmin)->delete("/academic-years/{$year->id}");

        $response->assertRedirect("/academic-years/{$year->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('academic_years', ['id' => $year->id]);
    }
}
