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
        Schema::create('pks_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('position', 50);
            $table->enum('status', ['active', 'inactive', 'graduated', 'resigned'])->default('active');
            $table->date('joined_at');
            $table->date('ended_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for common queries
            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'student_id']);
            $table->index('position');

            // Ensure one student can only have one active membership per school
            $table->unique(['school_id', 'student_id', 'status'], 'unique_active_membership');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pks_members');
    }
};
