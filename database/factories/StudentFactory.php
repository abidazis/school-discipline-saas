<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'school_class_id' => SchoolClass::factory(),
            'nis' => (string) fake()->unique()->numerify('####'),
            'nisn' => fake()->optional(0.8)->numerify('##########'),
            'full_name' => fake()->name(),
            'gender' => fake()->randomElement(['male', 'female']),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->dateTimeBetween('-20 years', '-12 years'),
            'address' => fake()->address(),
            'phone' => fake()->optional(0.7)->phoneNumber(),
            'status' => 'active',
            'photo' => null,
        ];
    }

    /**
     * Indicate that the student is male.
     */
    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'male',
        ]);
    }

    /**
     * Indicate that the student is female.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'female',
        ]);
    }

    /**
     * Indicate that the student is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the student has graduated.
     */
    public function graduated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'graduated',
        ]);
    }

    /**
     * Indicate that the student has transferred.
     */
    public function transferred(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'transferred',
        ]);
    }

    /**
     * Indicate that the student is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
