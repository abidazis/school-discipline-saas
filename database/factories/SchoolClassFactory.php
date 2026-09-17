<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolClass>
 */
class SchoolClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gradeLevels = ['VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $grade = fake()->randomElement($gradeLevels);
        $classNumber = fake()->numberBetween(1, 5);

        return [
            'school_id' => School::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'department_id' => fake()->boolean(70) ? Department::factory() : null,
            'name' => (string) $classNumber,
            'grade_level' => $grade,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the class is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
