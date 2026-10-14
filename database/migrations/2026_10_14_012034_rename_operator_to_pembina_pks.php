<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if running on MySQL
        $isMySQL = DB::connection()->getDriverName() === 'mysql';

        if ($isMySQL) {
            // MySQL: Change role column to VARCHAR to allow temporary value
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'teacher'");

            // Update operator to temp value
            DB::statement("UPDATE users SET role = 'temp_operator' WHERE role = 'operator'");

            // Change back to ENUM with new value
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'school_admin', 'pembina_pks', 'teacher', 'pks_team', 'temp_operator') NOT NULL DEFAULT 'teacher'");

            // Update temp to pembina_pks
            DB::statement("UPDATE users SET role = 'pembina_pks' WHERE role = 'temp_operator'");

            // Remove temp value from enum
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'school_admin', 'pembina_pks', 'teacher', 'pks_team') NOT NULL DEFAULT 'teacher'");
        } else {
            // SQLite/Other: Just update the string values
            DB::statement("UPDATE users SET role = 'pembina_pks' WHERE role = 'operator'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $isMySQL = DB::connection()->getDriverName() === 'mysql';

        if ($isMySQL) {
            // MySQL: Change role column to VARCHAR
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'teacher'");

            // Update pembina_pks to temp value
            DB::statement("UPDATE users SET role = 'temp_pembina_pks' WHERE role = 'pembina_pks'");

            // Change back to ENUM with old operator value
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'school_admin', 'operator', 'teacher', 'pks_team', 'temp_pembina_pks') NOT NULL DEFAULT 'teacher'");

            // Update temp to operator
            DB::statement("UPDATE users SET role = 'operator' WHERE role = 'temp_pembina_pks'");

            // Remove temp value from enum
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'school_admin', 'operator', 'teacher', 'pks_team') NOT NULL DEFAULT 'teacher'");
        } else {
            // SQLite/Other: Just update the string values
            DB::statement("UPDATE users SET role = 'operator' WHERE role = 'pembina_pks'");
        }
    }
};
