<?php

namespace Database\Seeders;

use App\Models\PksDutyAssignment;
use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksShift;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Models\ViolationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAccountSeeder extends Seeder
{
    /**
     * Run the test accounts seeder.
     *
     * This seeder creates isolated test accounts for each role.
     * All accounts use password: 'password'
     *
     * TEST ACCOUNTS:
     * - superadmin@test.local | Super Admin (global)
     * - schooladmin@test.local | Admin Sekolah (SMK Negeri 1 Jakarta)
     * - pembinapks@test.local | Pembina PKS (SMK Negeri 1 Jakarta)
     * - teacher@test.local | Guru (SMK Negeri 1 Jakarta)
     * - 10001@pks.local | PKS Team - Ketua (SMK Negeri 1 Jakarta) [existing]
     */
    public function run(): void
    {
        $this->command->info('Creating test accounts...');

        // Get school for testing (SMK Negeri 1 Jakarta)
        $school = School::where('slug', 'smk-negeri-1-jakarta')->first();

        if (!$school) {
            $this->command->warn('School SMK Negeri 1 Jakarta not found. Test accounts will be created without school.');
        }

        // ========================================
        // SUPER ADMIN
        // ========================================
        User::updateOrCreate(
            ['email' => 'superadmin@test.local'],
            [
                'name' => 'Test Super Admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SUPER_ADMIN,
                'school_id' => null,
                'email_verified_at' => now(),
            ]
        );
        $this->command->info('Created: superadmin@test.local (password: password)');

        // ========================================
        // SCHOOL ADMIN
        // ========================================
        if ($school) {
            User::updateOrCreate(
                ['email' => 'schooladmin@test.local'],
                [
                    'name' => 'Test School Admin',
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_SCHOOL_ADMIN,
                    'school_id' => $school->id,
                    'email_verified_at' => now(),
                ]
            );
            $this->command->info('Created: schooladmin@test.local (password: password) - School: ' . $school->name);

            // ========================================
            // PEMBINA PKS
            // ========================================
            User::updateOrCreate(
                ['email' => 'pembinapks@test.local'],
                [
                    'name' => 'Test Pembina PKS',
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_PEMBINA_PKS,
                    'school_id' => $school->id,
                    'email_verified_at' => now(),
                ]
            );
            $this->command->info('Created: pembinapks@test.local (password: password) - School: ' . $school->name);

            // ========================================
            // TEACHER
            // ========================================
            User::updateOrCreate(
                ['email' => 'teacher@test.local'],
                [
                    'name' => 'Test Teacher',
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_TEACHER,
                    'school_id' => $school->id,
                    'email_verified_at' => now(),
                ]
            );
            $this->command->info('Created: teacher@test.local (password: password) - School: ' . $school->name);
        }

        // ========================================
        // PKS TEAM - Verify existing account
        // ========================================
        $pksUser = User::where('email', '10001@pks.local')->first();
        if ($pksUser) {
            $this->command->info('PKS Team account exists: 10001@pks.local (password: 10001) - Role: ' . $pksUser->role);
        } else {
            $this->command->warn('PKS Team account 10001@pks.local not found. Run PksTeamSeeder first.');
        }

        $this->command->info('');
        $this->command->info('=== TEST ACCOUNTS SUMMARY ===');
        $this->command->info('All passwords: password');
        $this->command->info('');
        $this->command->info('| Email                    | Role         | School                     |');
        $this->command->info('|--------------------------|--------------|----------------------------|');
        $this->command->info('| superadmin@test.local    | Super Admin  | (global)                   |');
        if ($school) {
            $this->command->info('| schooladmin@test.local  | School Admin | SMK Negeri 1 Jakarta        |');
            $this->command->info('| pembinapks@test.local   | Pembina PKS | SMK Negeri 1 Jakarta        |');
            $this->command->info('| teacher@test.local      | Teacher     | SMK Negeri 1 Jakarta        |');
        }
        $this->command->info('| 10001@pks.local         | PKS Team     | SMK Negeri 1 Jakarta (NIS) |');
    }
}
