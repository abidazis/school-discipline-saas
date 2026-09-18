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
        Schema::create('pks_duty_schedule_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pks_duty_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pks_duty_location_id')->constrained('pks_duty_locations')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['pks_duty_schedule_id', 'pks_duty_location_id'], 'unique_schedule_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pks_duty_schedule_locations');
    }
};
