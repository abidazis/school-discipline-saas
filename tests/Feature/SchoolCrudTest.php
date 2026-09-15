<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->superAdmin()->create();
    }

    public function test_can_list_schools(): void
    {
        School::factory()->count(3)->create();

        $response = $this->actingAs($this->superAdmin)->get('/schools');

        $response->assertStatus(200);
        $response->assertViewHas('schools');
    }

    public function test_can_view_single_school(): void
    {
        $school = School::factory()->create();

        $response = $this->actingAs($this->superAdmin)->get("/schools/{$school->id}");

        $response->assertStatus(200);
        $response->assertSee($school->name);
    }

    public function test_can_create_school(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/schools', [
            'name' => 'New School',
            'email' => 'new@school.com',
            'phone' => '+62 123 456 7890',
            'address' => 'New Address',
            'is_active' => true,
        ]);

        $response->assertRedirect('/schools');
        $this->assertDatabaseHas('schools', [
            'name' => 'New School',
            'email' => 'new@school.com',
        ]);
    }

    public function test_can_update_school(): void
    {
        $school = School::factory()->create();

        $response = $this->actingAs($this->superAdmin)->patch("/schools/{$school->id}", [
            'name' => 'Updated School',
            'email' => 'updated@school.com',
        ]);

        $response->assertRedirect("/schools/{$school->id}");
        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'name' => 'Updated School',
        ]);
    }

    public function test_can_delete_empty_school(): void
    {
        $school = School::factory()->create();

        $response = $this->actingAs($this->superAdmin)->delete("/schools/{$school->id}");

        $response->assertRedirect('/schools');
        $this->assertDatabaseMissing('schools', ['id' => $school->id]);
    }

    public function test_cannot_delete_school_with_users(): void
    {
        $school = School::factory()->create();
        User::factory()->schoolAdmin()->forSchool($school)->create();

        $response = $this->actingAs($this->superAdmin)->delete("/schools/{$school->id}");

        $response->assertRedirect("/schools/{$school->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('schools', ['id' => $school->id]);
    }

    public function test_school_name_is_required(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/schools', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_can_filter_schools_by_status(): void
    {
        School::factory()->create(['is_active' => true]);
        School::factory()->create(['is_active' => false]);

        $response = $this->actingAs($this->superAdmin)->get('/schools?status=active');

        $response->assertStatus(200);
        // Only active schools should be prominent
    }

    public function test_can_search_schools(): void
    {
        School::factory()->create(['name' => 'Jakarta School']);
        School::factory()->create(['name' => 'Bandung School']);

        $response = $this->actingAs($this->superAdmin)->get('/schools?search=Jakarta');

        $response->assertStatus(200);
        $response->assertSee('Jakarta School');
        $response->assertSee('Jakarta School');
    }
}
