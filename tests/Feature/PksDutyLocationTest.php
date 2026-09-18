<?php

namespace Tests\Feature;

use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksShift;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PksDutyLocationTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected School $school2;
    protected User $superAdmin;
    protected User $schoolAdmin;
    protected User $operator;
    protected User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::factory()->create();
        $this->school2 = School::factory()->create();

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

    public function test_can_list_locations(): void
    {
        PksDutyLocation::factory()->for($this->school)->create(['name' => 'Gerbang Depan']);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-locations.index'));

        $response->assertStatus(200);
        $response->assertSee('Gerbang Depan');
    }

    public function test_can_create_location(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-locations.store'), [
            'name' => 'Gerbang Depan',
            'code' => 'GERBANG-DEPAN',
            'status' => 'active',
            'description' => 'Lokasi piket gerbang depan',
        ]);

        $response->assertRedirect(route('pks-duty-locations.index'));
        $this->assertDatabaseHas('pks_duty_locations', [
            'name' => 'Gerbang Depan',
            'code' => 'GERBANG-DEPAN',
            'status' => 'active',
            'school_id' => $this->school->id,
        ]);
    }

    public function test_can_view_location_detail(): void
    {
        $location = PksDutyLocation::factory()->for($this->school)->create([
            'name' => 'Gerbang Depan',
            'code' => 'GERBANG-DEPAN',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-locations.show', $location));

        $response->assertStatus(200);
        $response->assertSee('Gerbang Depan');
        $response->assertSee('GERBANG-DEPAN');
    }

    public function test_can_update_location(): void
    {
        $location = PksDutyLocation::factory()->for($this->school)->create([
            'name' => 'Gerbang Depan',
            'code' => 'GERBANG-DEPAN',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->patch(route('pks-duty-locations.update', $location), [
            'name' => 'Gerbang Belakang',
            'code' => 'GERBANG-BELAKANG',
            'status' => 'inactive',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect(route('pks-duty-locations.show', $location));
        $this->assertDatabaseHas('pks_duty_locations', [
            'id' => $location->id,
            'name' => 'Gerbang Belakang',
            'code' => 'GERBANG-BELAKANG',
            'status' => 'inactive',
        ]);
    }

    // ==========================================
    // Authorization Tests
    // ==========================================

    public function test_operator_cannot_create_location(): void
    {
        $response = $this->actingAs($this->operator)->post(route('pks-duty-locations.store'), [
            'name' => 'Gerbang Depan',
            'code' => 'GERBANG-DEPAN',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create_location(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('pks-duty-locations.store'), [
            'name' => 'Gerbang Depan',
            'code' => 'GERBANG-DEPAN',
        ]);

        $response->assertStatus(403);
    }

    public function test_operator_cannot_update_location(): void
    {
        $location = PksDutyLocation::factory()->for($this->school)->create();

        $response = $this->actingAs($this->operator)->patch(route('pks-duty-locations.update', $location), [
            'name' => 'Updated',
            'code' => 'UPDATED',
        ]);

        $response->assertStatus(403);
    }

    public function test_school_admin_cannot_update_other_school_location(): void
    {
        $locationSchool2 = PksDutyLocation::factory()->for($this->school2)->create();

        $response = $this->actingAs($this->schoolAdmin)->patch(route('pks-duty-locations.update', $locationSchool2), [
            'name' => 'Updated',
            'code' => 'UPDATED',
        ]);

        $response->assertStatus(404);
    }

    public function test_super_admin_can_update_location_from_any_school(): void
    {
        $locationSchool2 = PksDutyLocation::factory()->for($this->school2)->create([
            'name' => 'Gerbang',
            'code' => 'GERBANG',
        ]);

        $response = $this->actingAs($this->superAdmin)->patch(route('pks-duty-locations.update', $locationSchool2), [
            'name' => 'Gerbang Updated',
            'code' => 'GERBANG',
        ]);

        $response->assertRedirect(route('pks-duty-locations.show', $locationSchool2));
        $this->assertDatabaseHas('pks_duty_locations', [
            'id' => $locationSchool2->id,
            'name' => 'Gerbang Updated',
        ]);
    }

    // ==========================================
    // Validation Tests
    // ==========================================

    public function test_name_required(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-locations.store'), [
            'code' => 'GERBANG-DEPAN',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_code_required(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-locations.store'), [
            'name' => 'Gerbang Depan',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_code_max_length(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-locations.store'), [
            'name' => 'Gerbang Depan',
            'code' => str_repeat('A', 25),
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_status_must_be_valid(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-locations.store'), [
            'name' => 'Gerbang Depan',
            'code' => 'GERBANG-DEPAN',
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('status');
    }

    // ==========================================
    // Tenant Isolation Tests
    // ==========================================

    public function test_tenant_isolation_cannot_view_other_school_location(): void
    {
        $locationSchool2 = PksDutyLocation::factory()->for($this->school2)->create();

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-locations.show', $locationSchool2));

        $response->assertStatus(404);
    }

    public function test_tenant_isolation_list_only_shows_own_locations(): void
    {
        PksDutyLocation::factory()->for($this->school)->create(['name' => 'School1 Location']);
        PksDutyLocation::factory()->for($this->school2)->create(['name' => 'School2 Location']);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-locations.index'));

        $response->assertSee('School1 Location');
        $response->assertDontSee('School2 Location');
    }

    // ==========================================
    // Duplicate Prevention Tests
    // ==========================================

    public function test_cannot_create_duplicate_code_in_same_school(): void
    {
        PksDutyLocation::factory()->for($this->school)->create(['code' => 'GERBANG-DEPAN']);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-locations.store'), [
            'name' => 'Gerbang Depan 2',
            'code' => 'GERBANG-DEPAN',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_can_create_same_code_in_different_schools(): void
    {
        PksDutyLocation::factory()->for($this->school)->create(['code' => 'GERBANG-DEPAN']);
        $locationSchool2 = PksDutyLocation::factory()->for($this->school2)->create(['code' => 'GERBANG-DEPAN']);

        $response = $this->actingAs($this->superAdmin)->get(route('pks-duty-locations.show', $locationSchool2));

        $response->assertStatus(200);
    }

    // ==========================================
    // Status Tests
    // ==========================================

    public function test_active_location(): void
    {
        $location = PksDutyLocation::factory()->for($this->school)->create(['status' => 'active']);

        $this->assertTrue($location->isActive());
        $this->assertEquals('Aktif', $location->status_display);
    }

    public function test_inactive_location(): void
    {
        $location = PksDutyLocation::factory()->for($this->school)->create(['status' => 'inactive']);

        $this->assertFalse($location->isActive());
        $this->assertEquals('Tidak Aktif', $location->status_display);
    }

    public function test_active_scope(): void
    {
        PksDutyLocation::factory()->for($this->school)->create(['status' => 'active']);
        PksDutyLocation::factory()->for($this->school)->create(['status' => 'inactive']);

        $activeLocations = PksDutyLocation::active()->get();

        $this->assertCount(1, $activeLocations);
    }

    // ==========================================
    // Search and Filter Tests
    // ==========================================

    public function test_can_search_by_name(): void
    {
        PksDutyLocation::factory()->for($this->school)->create(['name' => 'Gerbang Depan']);
        PksDutyLocation::factory()->for($this->school)->create(['name' => 'Masjid']);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-locations.index', ['search' => 'Gerbang']));

        $response->assertSee('Gerbang Depan');
        $response->assertDontSee('Masjid');
    }

    public function test_can_search_by_code(): void
    {
        PksDutyLocation::factory()->for($this->school)->create(['name' => 'Gerbang Depan', 'code' => 'GERBANG-DEPAN']);
        PksDutyLocation::factory()->for($this->school)->create(['name' => 'Masjid', 'code' => 'MASJID']);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-locations.index', ['search' => 'GERBANG']));

        $response->assertSee('Gerbang Depan');
        $response->assertDontSee('Masjid');
    }

    public function test_can_filter_by_status(): void
    {
        PksDutyLocation::factory()->for($this->school)->create(['status' => 'active']);
        PksDutyLocation::factory()->for($this->school)->create(['status' => 'inactive']);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-locations.index', ['status' => 'active']));

        $response->assertSee('Aktif');
    }

    // ==========================================
    // Pagination Tests
    // ==========================================

    public function test_pagination_works(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            PksDutyLocation::factory()->for($this->school)->create([
                'name' => "Location $i",
                'code' => "LOC-$i",
            ]);
        }

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-locations.index'));

        $response->assertStatus(200);
        $response->assertSee('Location 1');
        // 15 per page, so Location 16 should not be on first page
    }

    // ==========================================
    // Relationship Tests
    // ==========================================

    public function test_location_belongs_to_school(): void
    {
        $location = PksDutyLocation::factory()->for($this->school)->create();

        $this->assertEquals($this->school->id, $location->school->id);
    }

    public function test_location_has_many_schedule_locations(): void
    {
        $location = PksDutyLocation::factory()->for($this->school)->create();
        $shift = PksShift::factory()->for($this->school)->create();
        $schedule1 = PksDutySchedule::factory()->for($this->school)->for($shift, 'shift')->create();
        $schedule2 = PksDutySchedule::factory()->for($this->school)->for($shift, 'shift')->create();
        $schedule1->locations()->attach($location);
        $schedule2->locations()->attach($location);

        $this->assertCount(2, $location->scheduleLocations);
    }
}
