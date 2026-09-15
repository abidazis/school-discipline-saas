<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCrudTest extends TestCase
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

    public function test_can_list_users(): void
    {
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->superAdmin)->get('/users');

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    public function test_can_view_single_user(): void
    {
        $user = User::factory()->forSchool($this->school)->create();

        $response = $this->actingAs($this->superAdmin)->get("/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_can_create_user(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'school_admin',
            'school_id' => $this->school->id,
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'role' => 'school_admin',
            'school_id' => $this->school->id,
        ]);
    }

    public function test_can_create_super_admin_without_school(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/users', [
            'name' => 'New Super Admin',
            'email' => 'newsuper@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'super_admin',
            'school_id' => '',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'name' => 'New Super Admin',
            'role' => 'super_admin',
            'school_id' => null,
        ]);
    }

    public function test_can_update_user(): void
    {
        $user = User::factory()->forSchool($this->school)->create();

        $response = $this->actingAs($this->superAdmin)->patch("/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => 'operator',
            'school_id' => $this->school->id,
        ]);

        $response->assertRedirect("/users/{$user->id}");
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'role' => 'operator',
        ]);
    }

    public function test_can_delete_user(): void
    {
        $user = User::factory()->forSchool($this->school)->create();

        $response = $this->actingAs($this->superAdmin)->delete("/users/{$user->id}");

        $response->assertRedirect('/users');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->superAdmin)->delete("/users/{$this->superAdmin->id}");

        $response->assertRedirect("/users/{$this->superAdmin->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);
    }

    public function test_user_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($this->superAdmin)->post('/users', [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'operator',
            'school_id' => $this->school->id,
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_can_filter_users_by_role(): void
    {
        User::factory()->superAdmin()->create();
        User::factory()->schoolAdmin()->forSchool($this->school)->create();

        $response = $this->actingAs($this->superAdmin)->get('/users?role=super_admin');

        $response->assertStatus(200);
        // Should only show super admin
    }

    public function test_can_filter_users_by_school(): void
    {
        $school2 = School::factory()->create();
        User::factory()->forSchool($this->school)->create();
        User::factory()->forSchool($school2)->create();

        $response = $this->actingAs($this->superAdmin)->get("/users?school_id={$this->school->id}");

        $response->assertStatus(200);
        // Should only show users from school1
    }

    public function test_password_is_optional_on_update(): void
    {
        $user = User::factory()->forSchool($this->school)->create();

        $response = $this->actingAs($this->superAdmin)->patch("/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => $user->role,
            'school_id' => $this->school->id,
        ]);

        $response->assertRedirect("/users/{$user->id}");
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }
}
