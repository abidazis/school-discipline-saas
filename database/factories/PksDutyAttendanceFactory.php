<?php

namespace Database\Factories;

use App\Models\PksDutyAssignment;
use App\Models\PksDutyAttendance;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PksDutyAttendance>
 */
class PksDutyAttendanceFactory extends Factory
{
    protected $model = PksDutyAttendance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => null, // Must be provided
            'pks_duty_assignment_id' => null, // Must be provided
            'status' => PksDutyAttendance::STATUS_PRESENT,
            'check_in_at' => now(),
            'check_out_at' => null,
            'notes' => null,
            'recorded_by' => null,
        ];
    }

    /**
     * Indicate that the attendance is present.
     */
    public function present(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksDutyAttendance::STATUS_PRESENT,
            'check_in_at' => now()->subMinutes(5),
            'check_out_at' => now()->addHours(3),
        ]);
    }

    /**
     * Indicate that the attendance is late.
     */
    public function late(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksDutyAttendance::STATUS_LATE,
            'check_in_at' => now()->addMinutes(15),
            'check_out_at' => now()->addHours(3),
        ]);
    }

    /**
     * Indicate that the attendance is absent.
     */
    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksDutyAttendance::STATUS_ABSENT,
            'check_in_at' => null,
            'check_out_at' => null,
        ]);
    }

    /**
     * Indicate that the attendance is excused.
     */
    public function excused(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksDutyAttendance::STATUS_EXCUSED,
            'check_in_at' => null,
            'check_out_at' => null,
            'notes' => 'Izin keluarga',
        ]);
    }

    /**
     * Attach to a specific assignment.
     */
    public function forAssignment(PksDutyAssignment $assignment): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $assignment->school_id,
            'pks_duty_assignment_id' => $assignment->id,
        ]);
    }
}
