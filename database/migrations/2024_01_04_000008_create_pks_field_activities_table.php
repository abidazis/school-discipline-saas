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
        Schema::create('pks_field_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pks_duty_assignment_id')->constrained('pks_duty_assignments')->cascadeOnDelete();
            $table->date('activity_date');
            $table->time('started_at');
            $table->time('ended_at')->nullable();
            $table->string('activity_type');
            $table->text('description')->nullable();
            $table->text('finding')->nullable();
            $table->text('action_taken')->nullable();
            $table->enum('status', ['draft', 'completed', 'cancelled'])->default('draft');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('violation_id')->nullable()->constrained('violations')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for common queries
            $table->index(['school_id', 'activity_date']);
            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'activity_type']);
            $table->index('pks_duty_assignment_id');
            $table->index('recorded_by');
            $table->index('violation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pks_field_activities');
    }
};
