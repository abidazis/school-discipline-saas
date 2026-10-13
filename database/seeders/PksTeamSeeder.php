<?php

namespace Database\Seeders;

use App\Models\PksMember;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PksTeamSeeder extends Seeder
{
    /**
     * Seed PKS Team members with user accounts.
     */
    public function run(): void
    {
        // Get active students from School 1 (SMK Negeri 1 Jakarta)
        $students = Student::where('school_id', 1)
            ->where('status', 'active')
            ->limit(5)
            ->get();

        $pksTeamData = [
            [
                'position' => PksMember::POSITION_KETUA,
                'name_suffix' => '(Ketua)',
            ],
            [
                'position' => PksMember::POSITION_WAKIL_KETUA,
                'name_suffix' => '(Wakill Ketua)',
            ],
            [
                'position' => PksMember::POSITION_KORLAP,
                'name_suffix' => '(Korlap)',
            ],
            [
                'position' => PksMember::POSITION_MEMBER,
                'name_suffix' => '(Anggota 1)',
            ],
            [
                'position' => PksMember::POSITION_MEMBER,
                'name_suffix' => '(Anggota 2)',
            ],
        ];

        foreach ($students as $index => $student) {
            // Generate email from NIS
            $email = strtolower($student->nis) . '@pks.local';

            // Create user account
            $user = User::create([
                'school_id' => $student->school_id,
                'name' => $student->full_name,
                'email' => $email,
                'password' => Hash::make($student->nis), // Password = NIS
                'role' => User::ROLE_PKS_TEAM,
                'email_verified_at' => now(),
            ]);

            // Create PKS member record
            $positionData = $pksTeamData[$index] ?? $pksTeamData[3]; // Default to anggota

            PksMember::create([
                'school_id' => $student->school_id,
                'user_id' => $user->id,
                'student_id' => $student->id,
                'position' => $positionData['position'],
                'status' => PksMember::STATUS_ACTIVE,
                'joined_at' => now()->subMonths(rand(1, 6)),
                'notes' => 'Anggota PKS dari seeder demo',
            ]);

            $this->command->info("Created PKS Team member: {$student->full_name}");
            $this->command->info("  -> Email: {$email}");
            $this->command->info("  -> Password: {$student->nis}");
            $this->command->info("  -> Position: {$positionData['position']}");
            $this->command->info("");
        }
    }
}
