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
        Schema::create('pks_duty_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pks_duty_assignment_id')->constrained('pks_duty_assignments')->cascadeOnDelete();
            $table->enum('status', ['present', 'late', 'absent', 'excused'])->default('present');
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes for common queries
            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'pks_duty_assignment_id']);
            $table->index('recorded_by');
            $table->index('check_in_at');

            // Unique constraint: one attendance per assignment
            $table->unique(['school_id', 'pks_duty_assignment_id'], 'unique_attendance_per_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pks_duty_attendances');
    }
};
