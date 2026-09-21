<?php

namespace Database\Factories;

use App\Models\PksDutyAssignment;
use App\Models\PksDutySchedule;
use App\Models\PksFieldActivity;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PksFieldActivity>
 */
class PksFieldActivityFactory extends Factory
{
    protected $model = PksFieldActivity::class;

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
            'activity_date' => now()->format('Y-m-d'),
            'started_at' => fake()->time('H:i'),
            'ended_at' => fake()->time('H:i'),
            'activity_type' => fake()->randomElement(array_keys(PksFieldActivity::TYPE_LABELS)),
            'description' => fake()->optional()->sentence(),
            'finding' => fake()->optional()->sentence(),
            'action_taken' => fake()->optional()->sentence(),
            'status' => PksFieldActivity::STATUS_COMPLETED,
            'recorded_by' => null,
            'violation_id' => null,
            'notes' => null,
        ];
    }

    /**
     * Indicate that the activity is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksFieldActivity::STATUS_DRAFT,
            'ended_at' => null,
        ]);
    }

    /**
     * Indicate that the activity is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksFieldActivity::STATUS_COMPLETED,
        ]);
    }

    /**
     * Indicate that the activity is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksFieldActivity::STATUS_CANCELLED,
        ]);
    }

    /**
     * Set monitoring activity type.
     */
    public function monitoring(): static
    {
        return $this->state(fn (array $attributes) => [
            'activity_type' => PksFieldActivity::TYPE_MONITORING,
            'description' => 'Melakukan monitoring di area yang ditentukan.',
            'finding' => fake()->optional()->sentence(),
            'action_taken' => fake()->optional()->sentence(),
        ]);
    }

    /**
     * Set patrol activity type.
     */
    public function patrol(): static
    {
        return $this->state(fn (array $attributes) => [
            'activity_type' => PksFieldActivity::TYPE_PATROL,
            'description' => 'Melakukan patroli rutin di area sekolah.',
            'finding' => fake()->optional()->sentence(),
            'action_taken' => fake()->optional()->sentence(),
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
