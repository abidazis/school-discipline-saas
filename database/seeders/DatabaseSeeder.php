<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
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
            'name' => 'SMK Negeri 1 Jakarta',
            'slug' => 'smk-negeri-1-jakarta',
            'address' => 'Jl. Sudirman No. 1, Jakarta Pusat',
            'phone' => '+62 21 1234 5678',
            'email' => 'info@smk1jkt.sch.id',
            'is_active' => true,
        ]);

        $school2 = School::create([
            'name' => 'SMK Negeri 2 Surabaya',
            'slug' => 'smk-negeri-2-surabaya',
            'address' => 'Jl. Pemuda No. 45, Surabaya',
            'phone' => '+62 31 9876 5432',
            'email' => 'info@smk2sby.sch.id',
            'is_active' => true,
        ]);

        // =====================
        // School 1 - SMK Negeri 1 Jakarta
        // =====================

        // Academic Years for School 1
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

        // Departments for School 1 (SMK)
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

        // Classes for School 1
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

        $class1_3 = SchoolClass::create([
            'school_id' => $school1->id,
            'academic_year_id' => $year1_2->id,
            'department_id' => $dept1_2->id,
            'name' => '1',
            'grade_level' => 'X',
            'is_active' => true,
        ]);

        $class1_4 = SchoolClass::create([
            'school_id' => $school1->id,
            'academic_year_id' => $year1_2->id,
            'department_id' => $dept1_3->id,
            'name' => '1',
            'grade_level' => 'XII',
            'is_active' => true,
        ]);

        // Students for School 1
        $maleNames = ['Ahmad Rizki', 'Budi Santoso', 'Dedi Kurniawan', 'Eko Prasetyo', 'Fajar Nugroho', 'Hadi Wijaya', 'Indra Permana', 'Joko Widodo'];
        $femaleNames = ['Ani Susilowati', 'Bella Putri', 'Citra Dewi', 'Dewi Lestari', 'Eka Sari', 'Fitri Handayani', 'Gita Rahman'];

        foreach ($maleNames as $index => $name) {
            Student::create([
                'school_id' => $school1->id,
                'academic_year_id' => $year1_2->id,
                'school_class_id' => $index < 3 ? $class1_1->id : $class1_2->id,
                'nis' => '1000' . ($index + 1),
                'nisn' => '0000' . ($index + 1000),
                'full_name' => $name,
                'gender' => 'male',
                'birth_place' => fake()->city(),
                'birth_date' => fake()->dateTimeBetween('-18 years', '-15 years'),
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'status' => 'active',
            ]);
        }

        foreach ($femaleNames as $index => $name) {
            Student::create([
                'school_id' => $school1->id,
                'academic_year_id' => $year1_2->id,
                'school_class_id' => $index < 2 ? $class1_3->id : $class1_4->id,
                'nis' => '2000' . ($index + 1),
                'nisn' => '0000' . ($index + 2000),
                'full_name' => $name,
                'gender' => 'female',
                'birth_place' => fake()->city(),
                'birth_date' => fake()->dateTimeBetween('-18 years', '-15 years'),
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'status' => 'active',
            ]);
        }

        // =====================
        // School 2 - SMK Negeri 2 Surabaya
        // =====================

        // Academic Years for School 2
        $year2_1 = AcademicYear::create([
            'school_id' => $school2->id,
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        // Departments for School 2
        $dept2_1 = Department::create([
            'school_id' => $school2->id,
            'name' => 'Rekayasa Perangkat Lunak',
            'code' => 'RPL',
            'is_active' => true,
        ]);

        $dept2_2 = Department::create([
            'school_id' => $school2->id,
            'name' => 'Tata Busana',
            'code' => 'TB',
            'is_active' => true,
        ]);

        // Classes for School 2
        $class2_1 = SchoolClass::create([
            'school_id' => $school2->id,
            'academic_year_id' => $year2_1->id,
            'department_id' => $dept2_1->id,
            'name' => '1',
            'grade_level' => 'X',
            'is_active' => true,
        ]);

        $class2_2 = SchoolClass::create([
            'school_id' => $school2->id,
            'academic_year_id' => $year2_1->id,
            'department_id' => $dept2_2->id,
            'name' => '1',
            'grade_level' => 'XI',
            'is_active' => true,
        ]);

        // Students for School 2
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
            'address' => 'Jl. Ketintang No. 10, Surabaya',
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        Student::create([
            'school_id' => $school2->id,
            'academic_year_id' => $year2_1->id,
            'school_class_id' => $class2_2->id,
            'nis' => '3002',
            'nisn' => '0003002',
            'full_name' => 'Rina Wulandari',
            'gender' => 'female',
            'birth_place' => 'Surabaya',
            'birth_date' => '2009-08-22',
            'address' => 'Jl. Pagesangan No. 5, Surabaya',
            'phone' => '081234567891',
            'status' => 'active',
        ]);

        // =====================
        // Users for Schools
        // =====================

        // School Admin for school1
        User::create([
            'name' => 'Admin SMK 1 Jakarta',
            'email' => 'admin@smk1jkt.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $school1->id,
            'email_verified_at' => now(),
        ]);

        // School Admin for school2
        User::create([
            'name' => 'Admin SMK 2 Surabaya',
            'email' => 'admin@smk2sby.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $school2->id,
            'email_verified_at' => now(),
        ]);

        // Operators
        User::create([
            'name' => 'Operator SMK 1 Jakarta',
            'email' => 'operator@smk1jkt.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_OPERATOR,
            'school_id' => $school1->id,
            'email_verified_at' => now(),
        ]);

        // Teachers
        User::create([
            'name' => 'Guru TKJ',
            'email' => 'guru.tkj@smk1jkt.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_TEACHER,
            'school_id' => $school1->id,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Demo data created successfully!');
        $this->command->info('');
        $this->command->info('=== DEMO ACCOUNTS ===');
        $this->command->info('Super Admin: super@example.com / password');
        $this->command->info('School Admin SMK 1: admin@smk1jkt.sch.id / password');
        $this->command->info('School Admin SMK 2: admin@smk2sby.sch.id / password');
        $this->command->info('Operator SMK 1: operator@smk1jkt.sch.id / password');
        $this->command->info('Teacher: guru.tkj@smk1jkt.sch.id / password');
        $this->command->info('');
        $this->command->info('=== SCHOOLS ===');
        $this->command->info('SMK Negeri 1 Jakarta - ' . Student::where('school_id', $school1->id)->count() . ' students');
        $this->command->info('SMK Negeri 2 Surabaya - ' . Student::where('school_id', $school2->id)->count() . ' students');
    }
}
