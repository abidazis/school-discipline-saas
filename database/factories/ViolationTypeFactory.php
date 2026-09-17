<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\ViolationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ViolationType>
 */
class ViolationTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ViolationType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            ViolationType::CATEGORY_ATTENDANCE,
            ViolationType::CATEGORY_UNIFORM,
            ViolationType::CATEGORY_BEHAVIOR,
            ViolationType::CATEGORY_SAFETY,
            ViolationType::CATEGORY_ACADEMIC,
            ViolationType::CATEGORY_TECHNOLOGY,
            ViolationType::CATEGORY_LEAVING_SCHOOL,
            ViolationType::CATEGORY_OTHER,
        ];

        $severities = [
            ViolationType::SEVERITY_LOW,
            ViolationType::SEVERITY_MEDIUM,
            ViolationType::SEVERITY_HIGH,
            ViolationType::SEVERITY_CRITICAL,
        ];

        return [
            'school_id' => School::factory(),
            'code' => strtoupper(fake()->unique()->lexify('??????')),
            'name' => fake()->sentence(3),
            'category' => fake()->randomElement($categories),
            'severity' => fake()->randomElement($severities),
            'points' => fake()->numberBetween(1, 20),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the violation type is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the violation type has low severity.
     */
    public function lowSeverity(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => ViolationType::SEVERITY_LOW,
            'points' => fake()->numberBetween(1, 3),
        ]);
    }

    /**
     * Indicate that the violation type has medium severity.
     */
    public function mediumSeverity(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => ViolationType::SEVERITY_MEDIUM,
            'points' => fake()->numberBetween(4, 7),
        ]);
    }

    /**
     * Indicate that the violation type has high severity.
     */
    public function highSeverity(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => ViolationType::SEVERITY_HIGH,
            'points' => fake()->numberBetween(8, 15),
        ]);
    }

    /**
     * Indicate that the violation type has critical severity.
     */
    public function criticalSeverity(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => ViolationType::SEVERITY_CRITICAL,
            'points' => fake()->numberBetween(16, 25),
        ]);
    }

    /**
     * Create a violation type for attendance category.
     */
    public function attendance(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => ViolationType::CATEGORY_ATTENDANCE,
            'code' => 'ATT',
        ]);
    }

    /**
     * Create a violation type for uniform category.
     */
    public function uniform(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => ViolationType::CATEGORY_UNIFORM,
            'code' => 'UNI',
        ]);
    }

    /**
     * Create a violation type for behavior category.
     */
    public function behavior(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => ViolationType::CATEGORY_BEHAVIOR,
            'code' => 'BEH',
        ]);
    }

    /**
     * Create a violation type for safety category.
     */
    public function safety(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => ViolationType::CATEGORY_SAFETY,
            'code' => 'SAF',
        ]);
    }
}
