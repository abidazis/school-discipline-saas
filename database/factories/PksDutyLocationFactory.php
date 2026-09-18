<?php

namespace Database\Factories;

use App\Models\PksDutyLocation;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PksDutyLocation>
 */
class PksDutyLocationFactory extends Factory
{
    protected $model = PksDutyLocation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $school = School::factory()->create();
        $code = strtoupper(fake()->unique()->lexify('??????'));

        return [
            'school_id' => $school->id,
            'name' => fake()->randomElement([
                'Gerbang Depan',
                'Gerbang Belakang',
                'Parkiran Depan',
                'Parkiran Belakang',
                'Masjid',
                'Toilet TKJ',
                'Toilet MP',
            ]),
            'code' => $code,
            'status' => PksDutyLocation::STATUS_ACTIVE,
            'description' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the location is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksDutyLocation::STATUS_ACTIVE,
        ]);
    }

    /**
     * Indicate that the location is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PksDutyLocation::STATUS_INACTIVE,
        ]);
    }
}
