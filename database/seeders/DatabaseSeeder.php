<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Super Admin (no school)
        User::create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPER_ADMIN,
            'school_id' => null,
            'email_verified_at' => now(),
        ]);

        // Create some schools
        $school1 = School::create([
            'name' => 'SMA Negeri 1 Jakarta',
            'slug' => 'sma-negeri-1-jakarta',
            'address' => 'Jl. Sudirman No. 1, Jakarta Pusat',
            'phone' => '+62 21 1234 5678',
            'email' => 'info@sman1jkt.sch.id',
            'is_active' => true,
        ]);

        $school2 = School::create([
            'name' => 'SMA Negeri 2 Surabaya',
            'slug' => 'sma-negeri-2-surabaya',
            'address' => 'Jl. Pemuda No. 45, Surabaya',
            'phone' => '+62 31 9876 5432',
            'email' => 'info@sman2sby.sch.id',
            'is_active' => true,
        ]);

        $school3 = School::create([
            'name' => 'SMP Islam Al-Hidayah',
            'slug' => 'smp-islam-al-hidayah',
            'address' => 'Jl. Hasanuddin No. 12, Bandung',
            'phone' => '+62 22 5555 1234',
            'email' => 'info@alhidayah.edu',
            'is_active' => true,
        ]);

        // Create School Admin for school1
        User::create([
            'name' => 'Admin SMA 1',
            'email' => 'admin@sman1jkt.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $school1->id,
            'email_verified_at' => now(),
        ]);

        // Create School Admin for school2
        User::create([
            'name' => 'Admin SMA 2',
            'email' => 'admin@sman2sby.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $school2->id,
            'email_verified_at' => now(),
        ]);

        // Create Operators
        User::create([
            'name' => 'Operator SMA 1',
            'email' => 'operator@sman1jkt.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_OPERATOR,
            'school_id' => $school1->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Operator SMP Hidayah',
            'email' => 'operator@alhidayah.edu',
            'password' => Hash::make('password'),
            'role' => User::ROLE_OPERATOR,
            'school_id' => $school3->id,
            'email_verified_at' => now(),
        ]);

        // Create Teachers
        User::create([
            'name' => 'Guru Bahasa Indonesia',
            'email' => 'guru@sman1jkt.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_TEACHER,
            'school_id' => $school1->id,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Demo users created:');
        $this->command->info('  Super Admin: super@example.com / password');
        $this->command->info('  School Admin: admin@sman1jkt.sch.id / password');
        $this->command->info('  School Admin: admin@sman2sby.sch.id / password');
        $this->command->info('  Operator: operator@sman1jkt.sch.id / password');
        $this->command->info('  Operator: operator@alhidayah.edu / password');
        $this->command->info('  Teacher: guru@sman1jkt.sch.id / password');
    }
}
