<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_schools_index(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->get('/schools');

        $response->assertStatus(200);
        $response->assertSee('Schools');
    }

    public function test_super_admin_can_access_users_index(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->get('/users');

        $response->assertStatus(200);
        $response->assertSee('Users');
    }

    public function test_super_admin_can_create_school(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->get('/schools/create');

        $response->assertStatus(200);
        $response->assertSee('Create New School');
    }

    public function test_super_admin_can_store_school(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->post('/schools', [
            'name' => 'Test School',
            'email' => 'test@school.com',
            'phone' => '+62 123 456 7890',
            'address' => 'Test Address',
            'is_active' => true,
        ]);

        $response->assertRedirect('/schools');
        $this->assertDatabaseHas('schools', [
            'name' => 'Test School',
            'email' => 'test@school.com',
        ]);
    }

    public function test_super_admin_can_create_user(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $school = School::factory()->create();

        $response = $this->actingAs($superAdmin)->get('/users/create');

        $response->assertStatus(200);
        $response->assertSee('Create New User');
    }

    public function test_school_admin_cannot_access_schools_index(): void
    {
        $school = School::factory()->create();
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($school)->create();

        $response = $this->actingAs($schoolAdmin)->get('/schools');

        $response->assertStatus(403);
    }

    public function test_school_admin_cannot_access_users_index(): void
    {
        $school = School::factory()->create();
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($school)->create();

        $response = $this->actingAs($schoolAdmin)->get('/users');

        $response->assertStatus(403);
    }

    public function test_school_admin_can_access_dashboard(): void
    {
        $school = School::factory()->create();
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($school)->create();

        $response = $this->actingAs($schoolAdmin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee($school->name);
    }

    public function test_operator_cannot_access_schools_index(): void
    {
        $school = School::factory()->create();
        $operator = User::factory()->operator()->forSchool($school)->create();

        $response = $this->actingAs($operator)->get('/schools');

        $response->assertStatus(403);
    }

    public function test_operator_cannot_access_users_index(): void
    {
        $school = School::factory()->create();
        $operator = User::factory()->operator()->forSchool($school)->create();

        $response = $this->actingAs($operator)->get('/users');

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_access_schools_index(): void
    {
        $school = School::factory()->create();
        $teacher = User::factory()->teacher()->forSchool($school)->create();

        $response = $this->actingAs($teacher)->get('/schools');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_protected_routes(): void
    {
        $protectedRoutes = [
            '/dashboard',
            '/schools',
            '/users',
            '/profile',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }
}
