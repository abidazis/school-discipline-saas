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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->string('nis'); // NIS - internal school ID
            $table->string('nisn', 20)->nullable(); // NISN - national student ID
            $table->string('full_name');
            $table->enum('gender', ['male', 'female']);
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('status', ['active', 'inactive', 'graduated', 'transferred'])->default('active');
            $table->string('photo')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'nis']);
            $table->index(['school_id', 'nisn']);
            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'academic_year_id']);
            $table->index(['school_id', 'school_class_id']);
            $table->unique(['school_id', 'nis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
