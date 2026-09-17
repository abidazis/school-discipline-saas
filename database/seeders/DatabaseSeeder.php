<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Models\ViolationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPER_ADMIN,
            'school_id' => null,
            'email_verified_at' => now(),
        ]);

        // Schools
        $school1 = School::create([
            'name' => 'SMK Negeri 1 Jakarta',
            'slug' => 'smk-negeri-1-jakarta',
            'address' => 'Jl. Sudirman No. 1',
            'phone' => '+62 21 1234 5678',
            'email' => 'info@smk1jkt.sch.id',
            'is_active' => true,
        ]);

        $school2 = School::create([
            'name' => 'SMK Negeri 2 Surabaya',
            'slug' => 'smk-negeri-2-surabaya',
            'address' => 'Jl. Pemuda No. 45',
            'phone' => '+62 31 9876 5432',
            'email' => 'info@smk2sby.sch.id',
            'is_active' => true,
        ]);

        // School 1: Academic Years
        $year1_1 = AcademicYear::create([
            'school_id' => $school1->id,
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ]);

        $year1_2 = AcademicYear::create([
            'school_id' => $school1->id,
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        // School 1: Departments
        $dept1_1 = Department::create([
            'school_id' => $school1->id,
            'name' => 'Teknik Komputer dan Jaringan',
            'code' => 'TKJ',
            'is_active' => true,
        ]);

        $dept1_2 = Department::create([
            'school_id' => $school1->id,
            'name' => 'Teknik Kendaraan Ringan',
            'code' => 'TKR',
            'is_active' => true,
        ]);

        $dept1_3 = Department::create([
            'school_id' => $school1->id,
            'name' => 'Akuntansi dan Keuangan Lembaga',
            'code' => 'AKL',
            'is_active' => true,
        ]);

        // School 1: Classes
        $class1_1 = SchoolClass::create([
            'school_id' => $school1->id,
            'academic_year_id' => $year1_2->id,
            'department_id' => $dept1_1->id,
            'name' => '1',
            'grade_level' => 'X',
            'is_active' => true,
        ]);

        $class1_2 = SchoolClass::create([
            'school_id' => $school1->id,
            'academic_year_id' => $year1_2->id,
            'department_id' => $dept1_1->id,
            'name' => '2',
            'grade_level' => 'XI',
            'is_active' => true,
        ]);

        // Violation Types for School 1
        ViolationType::create([
            'school_id' => $school1->id,
            'code' => 'LATE',
            'name' => 'Terlambat Masuk Sekolah',
            'category' => ViolationType::CATEGORY_ATTENDANCE,
            'severity' => ViolationType::SEVERITY_LOW,
            'points' => 2,
            'is_active' => true,
        ]);

        ViolationType::create([
            'school_id' => $school1->id,
            'code' => 'UNIFORM',
            'name' => 'Seragam Tidak Lengkap',
            'category' => ViolationType::CATEGORY_UNIFORM,
            'severity' => ViolationType::SEVERITY_LOW,
            'points' => 3,
            'is_active' => true,
        ]);

        ViolationType::create([
            'school_id' => $school1->id,
            'code' => 'HELMET',
            'name' => 'Tidak Helm',
            'category' => ViolationType::CATEGORY_SAFETY,
            'severity' => ViolationType::SEVERITY_MEDIUM,
            'points' => 5,
            'is_active' => true,
        ]);

        ViolationType::create([
            'school_id' => $school1->id,
            'code' => 'SMOKING',
            'name' => 'Merokok di Lingkungan Sekolah',
            'category' => ViolationType::CATEGORY_BEHAVIOR,
            'severity' => ViolationType::SEVERITY_HIGH,
            'points' => 10,
            'is_active' => true,
        ]);

        ViolationType::create([
            'school_id' => $school1->id,
            'code' => 'FIGHT',
            'name' => 'Perkelahian',
            'category' => ViolationType::CATEGORY_BEHAVIOR,
            'severity' => ViolationType::SEVERITY_CRITICAL,
            'points' => 20,
            'is_active' => true,
        ]);

        ViolationType::create([
            'school_id' => $school1->id,
            'code' => 'LEAVE',
            'name' => 'Pulang Sebelum Waktunya',
            'category' => ViolationType::CATEGORY_LEAVING_SCHOOL,
            'severity' => ViolationType::SEVERITY_MEDIUM,
            'points' => 5,
            'is_active' => true,
        ]);

        // Students School 1
        $maleNames = ['Ahmad Rizki', 'Budi Santoso', 'Dedi Kurniawan', 'Eko Prasetyo', 'Fajar Nugroho', 'Hadi Wijaya', 'Indra Permana', 'Joko Widodo'];
        $femaleNames = ['Ani Susilowati', 'Bella Putri', 'Citra Dewi', 'Dewi Lestari', 'Eka Sari', 'Fitri Handayani', 'Gita Rahman'];

        foreach ($maleNames as $idx => $name) {
            Student::create([
                'school_id' => $school1->id,
                'academic_year_id' => $year1_2->id,
                'school_class_id' => $idx < 3 ? $class1_1->id : $class1_2->id,
                'nis' => '1000' . ($idx + 1),
                'nisn' => '0000' . ($idx + 1000),
                'full_name' => $name,
                'gender' => 'male',
                'birth_place' => fake()->city(),
                'birth_date' => fake()->dateTimeBetween('-18 years', '-15 years'),
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'status' => 'active',
            ]);
        }

        foreach ($femaleNames as $idx => $name) {
            Student::create([
                'school_id' => $school1->id,
                'academic_year_id' => $year1_2->id,
                'school_class_id' => $idx < 2 ? $class1_1->id : $class1_2->id,
                'nis' => '2000' . ($idx + 1),
                'nisn' => '0000' . ($idx + 2000),
                'full_name' => $name,
                'gender' => 'female',
                'birth_place' => fake()->city(),
                'birth_date' => fake()->dateTimeBetween('-18 years', '-15 years'),
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'status' => 'active',
            ]);
        }

        // School 2: Academic Year
        $year2_1 = AcademicYear::create([
            'school_id' => $school2->id,
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        // School 2: Departments
        $dept2_1 = Department::create([
            'school_id' => $school2->id,
            'name' => 'Rekayasa Perangkat Lunak',
            'code' => 'RPL',
            'is_active' => true,
        ]);

        // School 2: Classes
        $class2_1 = SchoolClass::create([
            'school_id' => $school2->id,
            'academic_year_id' => $year2_1->id,
            'department_id' => $dept2_1->id,
            'name' => '1',
            'grade_level' => 'X',
            'is_active' => true,
        ]);

        // Violation Types School 2
        ViolationType::create([
            'school_id' => $school2->id,
            'code' => 'LATE',
            'name' => 'Terlambat Masuk Sekolah',
            'category' => ViolationType::CATEGORY_ATTENDANCE,
            'severity' => ViolationType::SEVERITY_LOW,
            'points' => 2,
            'is_active' => true,
        ]);

        // Students School 2
        Student::create([
            'school_id' => $school2->id,
            'academic_year_id' => $year2_1->id,
            'school_class_id' => $class2_1->id,
            'nis' => '3001',
            'nisn' => '0003001',
            'full_name' => 'Surya Darma',
            'gender' => 'male',
            'birth_place' => 'Surabaya',
            'birth_date' => '2010-05-15',
            'address' => 'Jl. Ketintang No. 10',
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        // Users
        User::create([
            'name' => 'Admin SMK 1 Jakarta',
            'email' => 'admin@smk1jkt.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $school1->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Admin SMK 2 Surabaya',
            'email' => 'admin@smk2sby.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $school2->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Operator SMK 1 Jakarta',
            'email' => 'operator@smk1jkt.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_OPERATOR,
            'school_id' => $school1->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Guru TKJ',
            'email' => 'guru.tkj@smk1jkt.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_TEACHER,
            'school_id' => $school1->id,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Demo data seeded.');
    }
}
