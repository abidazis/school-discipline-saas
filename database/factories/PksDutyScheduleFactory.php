<?php

namespace Database\Factories;

use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksShift;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PksDutySchedule>
 */
class PksDutyScheduleFactory extends Factory
{
    protected $model = PksDutySchedule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $school = School::factory()->create();
        $shift = PksShift::factory()->for($school)->create();
        $location = PksDutyLocation::factory()->for($school)->create();

        return [
            'school_id' => $school->id,
            'pks_shift_id' => $shift->id,
            'schedule_date' => fake()->date(),
            'status' => PksDutySchedule::STATUS_SCHEDULED,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the schedule is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksDutySchedule::STATUS_SCHEDULED,
        ]);
    }

    /**
     * Indicate that the schedule is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksDutySchedule::STATUS_COMPLETED,
        ]);
    }

    /**
     * Indicate that the schedule is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksDutySchedule::STATUS_CANCELLED,
        ]);
    }

    /**
     * Attach a location to the schedule.
     */
    public function withLocation(PksDutyLocation $location): static
    {
        return $this->afterCreating(function (PksDutySchedule $schedule) use ($location) {
            if ($schedule->school_id === $location->school_id) {
                $schedule->locations()->attach($location->id);
            }
        });
    }
}
