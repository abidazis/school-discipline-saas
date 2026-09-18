<?php

namespace Tests\Feature;

use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksShift;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PksShiftTest extends TestCase
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

    public function test_can_list_shifts(): void
    {
        PksShift::factory()->for($this->school)->create(['name' => 'Pagi']);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-shifts.index'));

        $response->assertStatus(200);
        $response->assertSee('Pagi');
    }

    public function test_can_create_shift(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-shifts.store'), [
            'name' => 'Pagi',
            'start_time' => '07:00',
            'end_time' => '10:00',
            'status' => 'active',
            'description' => 'Shift pagi',
        ]);

        $response->assertRedirect(route('pks-shifts.index'));
        $this->assertDatabaseHas('pks_shifts', [
            'name' => 'Pagi',
            'status' => 'active',
            'school_id' => $this->school->id,
        ]);
    }

    public function test_can_view_shift_detail(): void
    {
        $shift = PksShift::factory()->for($this->school)->create([
            'name' => 'Pagi',
            'start_time' => '07:00',
            'end_time' => '10:00',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-shifts.show', $shift));

        $response->assertStatus(200);
        $response->assertSee('Pagi');
        $response->assertSee('07:00');
        $response->assertSee('10:00');
    }

    public function test_can_update_shift(): void
    {
        $shift = PksShift::factory()->for($this->school)->create([
            'name' => 'Pagi',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->patch(route('pks-shifts.update', $shift), [
            'name' => 'Pagi Edit',
            'start_time' => '08:00',
            'end_time' => '11:00',
            'status' => 'inactive',
            'description' => 'Updated',
        ]);

        $response->assertRedirect(route('pks-shifts.show', $shift));
        $this->assertDatabaseHas('pks_shifts', [
            'id' => $shift->id,
            'name' => 'Pagi Edit',
            'status' => 'inactive',
        ]);
    }

    // ==========================================
    // Authorization Tests
    // ==========================================

    public function test_operator_cannot_create_shift(): void
    {
        $response = $this->actingAs($this->operator)->post(route('pks-shifts.store'), [
            'name' => 'Pagi',
            'start_time' => '07:00',
            'end_time' => '10:00',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create_shift(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('pks-shifts.store'), [
            'name' => 'Pagi',
            'start_time' => '07:00',
            'end_time' => '10:00',
        ]);

        $response->assertStatus(403);
    }

    public function test_operator_cannot_update_shift(): void
    {
        $shift = PksShift::factory()->for($this->school)->create();

        $response = $this->actingAs($this->operator)->patch(route('pks-shifts.update', $shift), [
            'name' => 'Updated',
            'start_time' => '07:00',
            'end_time' => '10:00',
        ]);

        $response->assertStatus(403);
    }

    public function test_school_admin_cannot_update_other_school_shift(): void
    {
        $shiftSchool2 = PksShift::factory()->for($this->school2)->create();

        $response = $this->actingAs($this->schoolAdmin)->patch(route('pks-shifts.update', $shiftSchool2), [
            'name' => 'Updated',
            'start_time' => '07:00',
            'end_time' => '10:00',
        ]);

        $response->assertStatus(404);
    }

    public function test_super_admin_can_update_shift_from_any_school(): void
    {
        $shiftSchool2 = PksShift::factory()->for($this->school2)->create(['name' => 'Pagi']);

        $response = $this->actingAs($this->superAdmin)->patch(route('pks-shifts.update', $shiftSchool2), [
            'name' => 'Pagi Updated',
            'start_time' => '07:00',
            'end_time' => '10:00',
        ]);

        $response->assertRedirect(route('pks-shifts.show', $shiftSchool2));
        $this->assertDatabaseHas('pks_shifts', [
            'id' => $shiftSchool2->id,
            'name' => 'Pagi Updated',
        ]);
    }

    // ==========================================
    // Validation Tests
    // ==========================================

    public function test_start_time_must_be_before_end_time(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-shifts.store'), [
            'name' => 'Invalid',
            'start_time' => '12:00',
            'end_time' => '07:00',
        ]);

        $response->assertSessionHasErrors('end_time');
    }

    public function test_time_format_validation(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-shifts.store'), [
            'name' => 'Test',
            'start_time' => 'invalid',
            'end_time' => '10:00',
        ]);

        $response->assertSessionHasErrors('start_time');
    }

    public function test_name_required(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-shifts.store'), [
            'start_time' => '07:00',
            'end_time' => '10:00',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_status_must_be_valid(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-shifts.store'), [
            'name' => 'Test',
            'start_time' => '07:00',
            'end_time' => '10:00',
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('status');
    }

    // ==========================================
    // Tenant Isolation Tests
    // ==========================================

    public function test_tenant_isolation_cannot_view_other_school_shift(): void
    {
        $shiftSchool2 = PksShift::factory()->for($this->school2)->create();

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-shifts.show', $shiftSchool2));

        $response->assertStatus(404);
    }

    public function test_tenant_isolation_list_only_shows_own_shifts(): void
    {
        PksShift::factory()->for($this->school)->create(['name' => 'School1 Shift']);
        PksShift::factory()->for($this->school2)->create(['name' => 'School2 Shift']);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-shifts.index'));

        $response->assertSee('School1 Shift');
        $response->assertDontSee('School2 Shift');
    }

    // ==========================================
    // Duplicate Prevention Tests
    // ==========================================

    public function test_cannot_create_duplicate_shift_name_in_same_school(): void
    {
        PksShift::factory()->for($this->school)->create(['name' => 'Pagi']);

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-shifts.store'), [
            'name' => 'Pagi',
            'start_time' => '08:00',
            'end_time' => '11:00',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_can_create_same_shift_name_in_different_schools(): void
    {
        PksShift::factory()->for($this->school)->create(['name' => 'Pagi']);
        $shiftSchool2 = PksShift::factory()->for($this->school2)->create(['name' => 'Pagi']);

        $response = $this->actingAs($this->superAdmin)->get(route('pks-shifts.show', $shiftSchool2));

        $response->assertStatus(200);
    }

    // ==========================================
    // Status Tests
    // ==========================================

    public function test_active_shift(): void
    {
        $shift = PksShift::factory()->for($this->school)->create(['status' => 'active']);

        $this->assertTrue($shift->isActive());
        $this->assertEquals('Aktif', $shift->status_display);
    }

    public function test_inactive_shift(): void
    {
        $shift = PksShift::factory()->for($this->school)->create(['status' => 'inactive']);

        $this->assertFalse($shift->isActive());
        $this->assertEquals('Tidak Aktif', $shift->status_display);
    }

    public function test_active_scope(): void
    {
        PksShift::factory()->for($this->school)->create(['name' => 'Pagi', 'status' => 'active']);
        PksShift::factory()->for($this->school)->create(['name' => 'Siang', 'status' => 'inactive']);

        $activeShifts = PksShift::active()->get();

        $this->assertCount(1, $activeShifts);
    }

    // ==========================================
    // Search and Filter Tests
    // ==========================================

    public function test_can_search_by_name(): void
    {
        PksShift::factory()->for($this->school)->create(['name' => 'Pagi']);
        PksShift::factory()->for($this->school)->create(['name' => 'Siang']);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-shifts.index', ['search' => 'Pagi']));

        $response->assertSee('Pagi');
        $response->assertDontSee('Siang');
    }

    public function test_can_filter_by_status(): void
    {
        PksShift::factory()->for($this->school)->create(['name' => 'FilterPagi', 'status' => 'active']);
        PksShift::factory()->for($this->school)->create(['name' => 'FilterSiang', 'status' => 'inactive']);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-shifts.index', ['status' => 'active']));

        $response->assertSee('Aktif');
    }

    // ==========================================
    // Relationship Tests
    // ==========================================

    public function test_shift_belongs_to_school(): void
    {
        $shift = PksShift::factory()->for($this->school)->create();

        $this->assertEquals($this->school->id, $shift->school->id);
    }

    public function test_shift_has_many_duty_schedules(): void
    {
        $shift = PksShift::factory()->for($this->school)->create();
        $location = PksDutyLocation::factory()->for($this->school)->create();
        $schedule1 = PksDutySchedule::factory()->for($this->school)->for($shift, 'shift')->create()->locations()->attach($location);
        $schedule2 = PksDutySchedule::factory()->for($this->school)->for($shift, 'shift')->create()->locations()->attach($location);

        $this->assertCount(2, $shift->dutySchedules);
    }

    public function test_time_range_attribute(): void
    {
        $shift = PksShift::factory()->for($this->school)->create([
            'start_time' => '07:00',
            'end_time' => '10:00',
        ]);

        $this->assertEquals('07:00 - 10:00', $shift->time_range);
    }
}
