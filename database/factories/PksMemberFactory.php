<?php

namespace Database\Factories;

use App\Models\PksMember;
use App\Models\School;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PksMember>
 */
class PksMemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PksMember::class;

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
            'position' => fake()->randomElement([
                PksMember::POSITION_MEMBER,
                PksMember::POSITION_KORLAP,
                PksMember::POSITION_KETUA,
            ]),
            'status' => PksMember::STATUS_ACTIVE,
            'joined_at' => fake()->dateTimeBetween('-2 years', '-1 month'),
            'ended_at' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the member is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksMember::STATUS_ACTIVE,
            'ended_at' => null,
        ]);
    }

    /**
     * Indicate that the member is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksMember::STATUS_INACTIVE,
            'ended_at' => fake()->dateTimeBetween($attributes['joined_at'] ?? '-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the member has graduated.
     */
    public function graduated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksMember::STATUS_GRADUATED,
            'ended_at' => fake()->dateTimeBetween($attributes['joined_at'] ?? '-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the member has resigned.
     */
    public function resigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksMember::STATUS_RESIGNED,
            'ended_at' => fake()->dateTimeBetween($attributes['joined_at'] ?? '-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the member is a korlap (Koordinator Lapangan).
     */
    public function korlap(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => PksMember::POSITION_KORLAP,
        ]);
    }

    /**
     * Indicate that the member is a chairman (Ketua).
     */
    public function chairman(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => PksMember::POSITION_KETUA,
        ]);
    }

    /**
     * Create a member for a specific student.
     */
    public function forStudent(Student $student): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $student->school_id,
            'student_id' => $student->id,
        ]);
    }
}
