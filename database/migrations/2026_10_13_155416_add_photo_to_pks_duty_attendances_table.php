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
        Schema::table('pks_duty_attendances', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('check_out_at');
            $table->string('photo_hash')->nullable()->after('photo_path');
            $table->timestamp('photo_captured_at')->nullable()->after('photo_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pks_duty_attendances', function (Blueprint $table) {
            $table->dropColumn(['photo_path', 'photo_hash', 'photo_captured_at']);
        });
    }
};
