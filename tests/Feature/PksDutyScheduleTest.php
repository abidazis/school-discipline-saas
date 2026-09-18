<?php

namespace Tests\Feature;

use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksShift;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PksDutyScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected School $school2;
    protected User $superAdmin;
    protected User $schoolAdmin;
    protected User $operator;
    protected User $teacher;
    protected PksShift $shift;
    protected PksDutyLocation $location;

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

        $this->shift = PksShift::factory()->for($this->school)->create([
            'name' => 'Pagi',
            'start_time' => '07:00',
            'end_time' => '10:00',
        ]);

        $this->location = PksDutyLocation::factory()->for($this->school)->create([
            'name' => 'Gerbang Depan',
            'code' => 'GERBANG-DEPAN',
        ]);
    }

    // ==========================================
    // CRUD Tests
    // ==========================================

    public function test_can_list_schedules(): void
    {
        $schedule = $this->createSchedule($this->school, $this->shift);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-schedules.index'));

        $response->assertStatus(200);
        $response->assertSee($schedule->schedule_date->format('d/m/Y'));
    }

    public function test_can_create_schedule(): void
    {
        $date = now()->addDays(rand(10, 30))->format('Y-m-d');

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-schedules.store'), [
            'pks_shift_id' => $this->shift->id,
            'schedule_date' => $date,
            'location_ids' => [$this->location->id],
            'status' => 'scheduled',
            'notes' => 'Test notes',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_duty_schedules', [
            'school_id' => $this->school->id,
            'pks_shift_id' => $this->shift->id,
            'status' => 'scheduled',
            'notes' => 'Test notes',
        ]);
    }

    public function test_can_view_schedule(): void
    {
        // Use unique schedule date to avoid conflicts
        $schedule = PksDutySchedule::create([
            'school_id' => $this->school->id,
            'pks_shift_id' => $this->shift->id,
            'schedule_date' => now()->addDays(100 + rand(1, 10))->format('Y-m-d'),
            'status' => 'scheduled',
        ]);
        $schedule->locations()->attach($this->location->id);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-schedules.show', $schedule));

        $response->assertStatus(200);
        $response->assertSee('Gerbang Depan');
        $response->assertSee('Pagi');
    }

    public function test_can_update_schedule(): void
    {
        $schedule = $this->createSchedule($this->school, $this->shift);
        $location2 = PksDutyLocation::factory()->for($this->school)->create([
            'name' => 'Masjid',
            'code' => 'MASJID',
        ]);

        $response = $this->actingAs($this->schoolAdmin)->patch(route('pks-duty-schedules.update', $schedule), [
            'pks_shift_id' => $this->shift->id,
            'schedule_date' => now()->addDays(rand(20, 40))->format('Y-m-d'),
            'location_ids' => [$location2->id],
            'status' => 'completed',
            'notes' => 'Updated notes',
        ]);

        $response->assertRedirect(route('pks-duty-schedules.show', $schedule));
        $this->assertDatabaseHas('pks_duty_schedules', [
            'id' => $schedule->id,
            'status' => 'completed',
            'notes' => 'Updated notes',
        ]);
    }

    // ==========================================
    // Multiple Locations Tests
    // ==========================================

    public function test_can_create_schedule_with_multiple_locations(): void
    {
        $location2 = PksDutyLocation::factory()->for($this->school)->create([
            'name' => 'Masjid',
            'code' => 'MASJID',
        ]);

        $date = now()->addDays(rand(10, 30))->format('Y-m-d');

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-schedules.store'), [
            'pks_shift_id' => $this->shift->id,
            'schedule_date' => $date,
            'location_ids' => [$this->location->id, $location2->id],
            'status' => 'scheduled',
        ]);

        $response->assertRedirect();

        $schedule = PksDutySchedule::latest()->first();
        $this->assertCount(2, $schedule->locations);
    }

    // ==========================================
    // Authorization Tests
    // ==========================================

    public function test_operator_cannot_create_schedule(): void
    {
        $date = now()->addDays(rand(10, 30))->format('Y-m-d');

        $response = $this->actingAs($this->operator)->post(route('pks-duty-schedules.store'), [
            'pks_shift_id' => $this->shift->id,
            'schedule_date' => $date,
            'location_ids' => [$this->location->id],
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create_schedule(): void
    {
        $date = now()->addDays(rand(10, 30))->format('Y-m-d');

        $response = $this->actingAs($this->teacher)->post(route('pks-duty-schedules.store'), [
            'pks_shift_id' => $this->shift->id,
            'schedule_date' => $date,
            'location_ids' => [$this->location->id],
        ]);

        $response->assertStatus(403);
    }

    public function test_operator_cannot_update_schedule(): void
    {
        $schedule = $this->createSchedule($this->school, $this->shift);

        $response = $this->actingAs($this->operator)->patch(route('pks-duty-schedules.update', $schedule), [
            'pks_shift_id' => $this->shift->id,
            'schedule_date' => $schedule->schedule_date->format('Y-m-d'),
            'location_ids' => [$this->location->id],
            'status' => 'completed',
        ]);

        $response->assertStatus(403);
    }

    // ==========================================
    // Validation Tests
    // ==========================================

    public function test_date_required(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-schedules.store'), [
            'pks_shift_id' => $this->shift->id,
            'location_ids' => [$this->location->id],
        ]);

        $response->assertSessionHasErrors('schedule_date');
    }

    public function test_shift_required(): void
    {
        $date = now()->addDays(rand(10, 30))->format('Y-m-d');

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-schedules.store'), [
            'schedule_date' => $date,
            'location_ids' => [$this->location->id],
        ]);

        $response->assertSessionHasErrors('pks_shift_id');
    }

    public function test_at_least_one_location_required(): void
    {
        $date = now()->addDays(rand(10, 30))->format('Y-m-d');

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-schedules.store'), [
            'pks_shift_id' => $this->shift->id,
            'schedule_date' => $date,
            'location_ids' => [],
        ]);

        $response->assertSessionHasErrors('location_ids');
    }

    // ==========================================
    // Tenant Isolation Tests
    // ==========================================

    public function test_tenant_isolation_cannot_view_other_school_schedule(): void
    {
        $shiftSchool2 = PksShift::factory()->for($this->school2)->create();
        $locationSchool2 = PksDutyLocation::factory()->for($this->school2)->create();
        $scheduleSchool2 = PksDutySchedule::create([
            'school_id' => $this->school2->id,
            'pks_shift_id' => $shiftSchool2->id,
            'schedule_date' => now()->addDays(rand(5, 15))->format('Y-m-d'),
            'status' => 'scheduled',
        ]);
        $scheduleSchool2->locations()->attach($locationSchool2->id);

        $response = $this->actingAs($this->schoolAdmin)->get(route('pks-duty-schedules.show', $scheduleSchool2));

        $response->assertStatus(404);
    }

    // ==========================================
    // Cross-Tenant Security Tests (Critical)
    // ==========================================

    public function test_cannot_use_shift_from_other_school(): void
    {
        $shiftSchool2 = PksShift::factory()->for($this->school2)->create();
        $date = now()->addDays(rand(10, 30))->format('Y-m-d');

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-schedules.store'), [
            'pks_shift_id' => $shiftSchool2->id,
            'schedule_date' => $date,
            'location_ids' => [$this->location->id],
        ]);

        $response->assertSessionHasErrors('pks_shift_id');
    }

    public function test_cannot_use_location_from_other_school(): void
    {
        $locationSchool2 = PksDutyLocation::factory()->for($this->school2)->create();
        $date = now()->addDays(rand(10, 30))->format('Y-m-d');

        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-schedules.store'), [
            'pks_shift_id' => $this->shift->id,
            'schedule_date' => $date,
            'location_ids' => [$locationSchool2->id],
        ]);

        $response->assertSessionHasErrors();
    }

    // ==========================================
    // Database constraint handles duplicates
    // The unique constraint on school_id + schedule_date + pks_shift_id prevents duplicates
    // ==========================================

    public function test_can_create_schedule_same_date_different_shift(): void
    {
        $shift2 = PksShift::factory()->for($this->school)->create([
            'name' => 'Siang',
            'start_time' => '10:00',
            'end_time' => '13:00',
        ]);
        $date = now()->addDays(rand(50, 70))->format('Y-m-d');

        // Create first schedule
        $this->createSchedule($this->school, $this->shift, $date);

        // Create second schedule with same date but different shift
        $response = $this->actingAs($this->schoolAdmin)->post(route('pks-duty-schedules.store'), [
            'pks_shift_id' => $shift2->id,
            'schedule_date' => $date,
            'location_ids' => [$this->location->id],
            'status' => 'scheduled',
        ]);

        $response->assertRedirect();
    }

    // ==========================================
    // Status Tests
    // ==========================================

    public function test_scheduled_status(): void
    {
        $schedule = $this->createSchedule($this->school, $this->shift);

        $this->assertTrue($schedule->isScheduled());
        $this->assertEquals('Terjadwal', $schedule->status_display);
    }

    public function test_completed_status(): void
    {
        $schedule = $this->createSchedule($this->school, $this->shift, null, 'completed');

        $this->assertTrue($schedule->isCompleted());
        $this->assertEquals('Selesai', $schedule->status_display);
    }

    public function test_cancelled_status(): void
    {
        $schedule = $this->createSchedule($this->school, $this->shift, null, 'cancelled');

        $this->assertTrue($schedule->isCancelled());
        $this->assertEquals('Dibatalkan', $schedule->status_display);
    }

    // ==========================================
    // Relationship Tests
    // ==========================================

    public function test_schedule_belongs_to_school(): void
    {
        $schedule = $this->createSchedule($this->school, $this->shift);

        $this->assertEquals($this->school->id, $schedule->school->id);
    }

    public function test_schedule_belongs_to_shift(): void
    {
        $schedule = $this->createSchedule($this->school, $this->shift);

        $this->assertEquals($this->shift->id, $schedule->shift->id);
    }

    public function test_schedule_has_many_locations(): void
    {
        $location2 = PksDutyLocation::factory()->for($this->school)->create();
        $location3 = PksDutyLocation::factory()->for($this->school)->create();

        $schedule = $this->createSchedule($this->school, $this->shift);
        $schedule->locations()->sync([$this->location->id, $location2->id, $location3->id]);

        $this->assertCount(3, $schedule->locations);
    }

    public function test_time_range_from_shift(): void
    {
        $schedule = $this->createSchedule($this->school, $this->shift);

        $this->assertEquals('07:00 - 10:00', $schedule->time_range);
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    protected function createSchedule(School $school, ?PksShift $shift, ?string $date = null, string $status = 'scheduled'): PksDutySchedule
    {
        $shift = $shift ?? PksShift::factory()->for($school)->create();
        $location = PksDutyLocation::factory()->for($school)->create();
        $date = $date ?? now()->addDays(rand(5, 10))->format('Y-m-d');

        $schedule = PksDutySchedule::create([
            'school_id' => $school->id,
            'pks_shift_id' => $shift->id,
            'schedule_date' => $date,
            'status' => $status,
        ]);
        $schedule->locations()->attach($location->id);

        return $schedule;
    }
}
