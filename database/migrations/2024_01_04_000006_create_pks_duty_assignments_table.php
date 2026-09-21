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
        Schema::create('pks_duty_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pks_duty_schedule_id')->constrained('pks_duty_schedules')->cascadeOnDelete();
            $table->foreignId('pks_member_id')->constrained('pks_members')->cascadeOnDelete();
            $table->foreignId('pks_duty_location_id')->constrained('pks_duty_locations')->cascadeOnDelete();
            $table->enum('status', ['assigned', 'replaced', 'cancelled'])->default('assigned');
            $table->timestamp('assigned_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for common queries
            $table->index(['school_id', 'pks_duty_schedule_id']);
            $table->index(['school_id', 'pks_member_id']);
            $table->index(['school_id', 'pks_duty_location_id']);
            $table->index('status');

            // Unique constraint: one member can only be assigned once per schedule
            // This prevents duplicate active assignments for the same member+schedule
            $table->unique(
                ['school_id', 'pks_duty_schedule_id', 'pks_member_id', 'status'],
                'unique_active_member_schedule'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pks_duty_assignments');
    }
};
