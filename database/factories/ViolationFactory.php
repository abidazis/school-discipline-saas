<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Violation>
 */
class ViolationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Violation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $school = School::factory()->create();

        return [
            'school_id' => $school->id,
            'student_id' => Student::factory()->for($school)->create()->id,
            'violation_type_id' => ViolationType::factory()->for($school)->create()->id,
            'academic_year_id' => AcademicYear::factory()->for($school)->create()->id,
            'school_class_id' => SchoolClass::factory()->for($school)->create()->id,
            'officer_id' => User::factory()->for($school)->create()->id,
            'occurred_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'location' => fake()->optional()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'points' => fake()->numberBetween(1, 10),
            'status' => Violation::STATUS_RECORDED,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Violation $violation) {
            // This runs after making the model
        })->afterCreating(function (Violation $violation) {
            // This runs after creating the model
        });
    }

    /**
     * Indicate that the violation is recorded.
     */
    public function recorded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Violation::STATUS_RECORDED,
        ]);
    }

    /**
     * Indicate that the violation is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Violation::STATUS_VERIFIED,
        ]);
    }

    /**
     * Indicate that the violation is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Violation::STATUS_CANCELLED,
            'cancelled_reason' => fake()->sentence(),
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Create a violation with a specific point value.
     */
    public function withPoints(int $points): static
    {
        return $this->state(fn (array $attributes) => [
            'points' => $points,
        ]);
    }

    /**
     * Create a violation for a specific student.
     */
    public function forStudent(Student $student): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'academic_year_id' => $student->academic_year_id,
            'school_class_id' => $student->school_class_id,
        ]);
    }

    /**
     * Create a violation for a specific violation type.
     */
    public function forViolationType(ViolationType $violationType): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $violationType->school_id,
            'violation_type_id' => $violationType->id,
            'points' => $violationType->points,
        ]);
    }

    /**
     * Create a violation for a specific officer.
     */
    public function forOfficer(User $officer): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $officer->school_id,
            'officer_id' => $officer->id,
        ]);
    }
}

