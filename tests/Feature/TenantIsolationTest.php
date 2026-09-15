<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_has_no_school_by_default(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->assertNull($superAdmin->school_id);
        $this->assertTrue($superAdmin->isSuperAdmin());
    }

    public function test_school_admin_belongs_to_school(): void
    {
        $school = School::factory()->create();
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($school)->create();

        $this->assertEquals($school->id, $schoolAdmin->school_id);
        $this->assertEquals($school->id, $schoolAdmin->school->id);
    }

    public function test_user_role_checks(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $schoolAdmin = User::factory()->schoolAdmin()->create();
        $operator = User::factory()->operator()->create();
        $teacher = User::factory()->teacher()->create();

        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertFalse($superAdmin->isSchoolAdmin());
        $this->assertFalse($superAdmin->isOperator());
        $this->assertFalse($superAdmin->isTeacher());

        $this->assertTrue($schoolAdmin->isSchoolAdmin());
        $this->assertTrue($operator->isOperator());
        $this->assertTrue($teacher->isTeacher());
    }

    public function test_super_admin_has_access_to_all_schools(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $school = School::factory()->create();

        $this->assertTrue($superAdmin->hasAccessToAllSchools());
        $this->assertTrue($superAdmin->canManageSchool($school->id));
        $this->assertTrue($superAdmin->canManageSchool(null));
    }

    public function test_school_admin_can_only_manage_own_school(): void
    {
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();
        $schoolAdmin = User::factory()->schoolAdmin()->forSchool($school1)->create();

        $this->assertTrue($schoolAdmin->canManageSchool($school1->id));
        $this->assertFalse($schoolAdmin->canManageSchool($school2->id));
    }

    public function test_school_model_relationships(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->schoolAdmin()->forSchool($school)->create();
        $operator = User::factory()->operator()->forSchool($school)->create();

        $this->assertCount(2, $school->users);
        $this->assertCount(1, $school->admins);
        $this->assertEquals($admin->id, $school->admins->first()->id);
    }

    public function test_school_can_be_active_or_inactive(): void
    {
        $activeSchool = School::factory()->create(['is_active' => true]);
        $inactiveSchool = School::factory()->inactive()->create();

        $this->assertTrue($activeSchool->is_active);
        $this->assertFalse($inactiveSchool->is_active);
    }

    public function test_user_can_be_created_without_school(): void
    {
        $user = User::factory()->create(['school_id' => null]);

        $this->assertNull($user->school);
    }
}
