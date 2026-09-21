<?php

namespace Database\Factories;

use App\Models\PksDutyAssignment;
use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksMember;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PksDutyAssignment>
 */
class PksDutyAssignmentFactory extends Factory
{
    protected $model = PksDutyAssignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => null, // Must be provided
            'pks_duty_schedule_id' => null, // Must be provided
            'pks_member_id' => null, // Must be provided
            'pks_duty_location_id' => null, // Must be provided
            'status' => PksDutyAssignment::STATUS_ASSIGNED,
            'assigned_at' => now(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the assignment is assigned.
     */
    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksDutyAssignment::STATUS_ASSIGNED,
        ]);
    }

    /**
     * Indicate that the assignment is replaced.
     */
    public function replaced(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksDutyAssignment::STATUS_REPLACED,
        ]);
    }

    /**
     * Indicate that the assignment is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksDutyAssignment::STATUS_CANCELLED,
        ]);
    }

    /**
     * Attach to a specific schedule.
     */
    public function forSchedule(PksDutySchedule $schedule): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $schedule->school_id,
            'pks_duty_schedule_id' => $schedule->id,
        ]);
    }

    /**
     * Attach to a specific schedule with location attachment.
     */
    public function forScheduleWithLocation(PksDutySchedule $schedule, PksDutyLocation $location): static
    {
        return $this->afterCreating(function (PksDutyAssignment $assignment) use ($schedule, $location) {
            if ($assignment->pks_duty_schedule_id === $schedule->id && $assignment->pks_duty_location_id === $location->id) {
                return;
            }
            // Detach from any existing locations
            $schedule->locations()->detach();
            // Attach the location
            $schedule->locations()->attach($location->id);
            $assignment->update([
                'pks_duty_schedule_id' => $schedule->id,
                'pks_duty_location_id' => $location->id,
            ]);
        });
    }

    /**
     * Assign a specific member.
     */
    public function forMember(PksMember $member): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $member->school_id,
            'pks_member_id' => $member->id,
        ]);
    }

    /**
     * Assign to a specific location.
     */
    public function atLocation(PksDutyLocation $location): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $location->school_id,
            'pks_duty_location_id' => $location->id,
        ]);
    }
}
