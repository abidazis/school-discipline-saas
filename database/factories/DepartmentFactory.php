<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments = [
            ['code' => 'TKJ', 'name' => 'Teknik Komputer dan Jaringan'],
            ['code' => 'TKR', 'name' => 'Teknik Kendaraan Ringan'],
            ['code' => 'AKL', 'name' => 'Akuntansi dan Keuangan Lembaga'],
            ['code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak'],
            ['code' => 'TB', 'name' => 'Tata Busana'],
            ['code' => 'AP', 'name' => 'Administrasi Perkantoran'],
            ['code' => 'MM', 'name' => 'Multimedia'],
            ['code' => 'DKV', 'name' => 'Desain Komunikasi Visual'],
        ];

        $dept = fake()->randomElement($departments);
        $suffix = fake()->unique()->numberBetween(1, 1000);

        return [
            'school_id' => School::factory(),
            'name' => $dept['name'],
            'code' => $dept['code'] . $suffix,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the department is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
