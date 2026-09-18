<?php

namespace Database\Factories;

use App\Models\PksShift;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PksShift>
 */
class PksShiftFactory extends Factory
{
    protected $model = PksShift::class;

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
            'name' => fake()->randomElement(['Pagi', 'Siang', 'Istirahat', 'Pulang']),
            'start_time' => fake()->time('H:i'),
            'end_time' => fake()->time('H:i'),
            'status' => PksShift::STATUS_ACTIVE,
            'description' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the shift is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksShift::STATUS_ACTIVE,
        ]);
    }

    /**
     * Indicate that the shift is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksShift::STATUS_INACTIVE,
        ]);
    }
}
