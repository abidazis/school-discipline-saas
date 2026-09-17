<?php

namespace Database\Factories;

use App\Models\Violation;
use App\Models\ViolationEvidence;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ViolationEvidence>
 */
class ViolationEvidenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ViolationEvidence::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'violation_id' => Violation::factory(),
            'file_path' => 'violations/' . fake()->uuid() . '.jpg',
            'original_name' => fake()->word() . '.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(10000, 5000000),
        ];
    }

    /**
     * Create evidence with a specific mime type.
     */
    public function withMimeType(string $mimeType): static
    {
        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        return $this->state(fn (array $attributes) => [
            'mime_type' => $mimeType,
            'original_name' => fake()->word() . '.' . $extension,
        ]);
    }

    /**
     * Create JPEG evidence.
     */
    public function jpeg(): static
    {
        return $this->withMimeType('image/jpeg');
    }

    /**
     * Create PNG evidence.
     */
    public function png(): static
    {
        return $this->withMimeType('image/png');
    }

    /**
     * Create WebP evidence.
     */
    public function webp(): static
    {
        return $this->withMimeType('image/webp');
    }

    /**
     * Create evidence for a specific violation.
     */
    public function forViolation(Violation $violation): static
    {
        return $this->state(fn (array $attributes) => [
            'violation_id' => $violation->id,
            'file_path' => 'violations/' . $violation->school_id . '/' . $violation->id . '/' . fake()->uuid() . '.jpg',
        ]);
    }
}
