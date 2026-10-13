<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update role enum di tabel users untuk menambahkan 'pks_team'
        Schema::table('users', function (Blueprint $table) {
            // Drop existing enum and recreate with pks_team
            $table->enum('role', ['super_admin', 'school_admin', 'operator', 'teacher', 'pks_team'])
                  ->default('operator')
                  ->change();
        });

        // 2. Tambahkan user_id foreign key di tabel pks_members
        Schema::table('pks_members', function (Blueprint $table) {
            $table->foreignId('user_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Drop unique constraint yang lama (hanya untuk active status)
            // Karena constraint unique_active_membership tidak memperhitungkan user_id
            // Kita perlu ubah strateginya: gunakan soft check di model
            $table->dropUnique('unique_active_membership');

            // Tambahkan unique constraint baru: student_id + status active per school
            // Ini akan ditangani oleh application logic untuk lebih fleksibel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pks_members', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'school_admin', 'operator', 'teacher'])
                  ->default('operator')
                  ->change();
        });
    }
};
