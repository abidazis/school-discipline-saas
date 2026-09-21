<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_route_redirects_unauthenticated_to_login(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_root_route_redirects_authenticated_to_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_SCHOOL_ADMIN,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_dashboard_loads_for_authenticated_user(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create([
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $school->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_dashboard_shows_user_name(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Test User');
    }

    public function test_dashboard_shows_stats_for_school_admin(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create([
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $school->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Siswa Aktif');
        $response->assertSee('Pelanggaran');
        $response->assertSee('Anggota PKS');
        $response->assertSee('Jadwal Piket');
    }

    public function test_dashboard_shows_quick_menu_for_school_admin(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create([
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $school->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Daftar Siswa');
        $response->assertSee('Shift Piket');
        $response->assertSee('Lokasi Piket');
        $response->assertSee('Jadwal Piket');
    }

    public function test_teacher_sees_read_only_menu(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create([
            'role' => User::ROLE_TEACHER,
            'school_id' => $school->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Daftar Siswa');
        // Teacher should not see create/edit options in dashboard
    }
}
