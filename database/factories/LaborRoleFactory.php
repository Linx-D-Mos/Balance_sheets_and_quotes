<?php

namespace Database\Factories;

use App\Models\LaborRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LaborRole>
 */
class LaborRoleFactory extends Factory
{
    protected $model = LaborRole::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $baseSalary = $this->faker->randomFloat(4, 18, 45);
        $socialLoad = 15.0000;
        $hourlyCost = $baseSalary * (1 + ($socialLoad / 100));

        return [
            'name' => $this->faker->unique()->jobTitle(),
            'base_salary' => $baseSalary,
            'social_load_pct' => $socialLoad,
            'hourly_cost' => round($hourlyCost, 4),
            'is_active' => true,
        ];
    }
    public function painter(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Pintor Principal',
            'base_salary' => 20.0000,
            'social_load_pct' => 15.0000,
            'hourly_cost' => 23.0000,
        ]);
    }

    public function foreman(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Capataz de Obra',
            'base_salary' => 32.0000,
            'social_load_pct' => 15.0000,
            'hourly_cost' => 36.8000,
        ]);
    }

    public function helper(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Ayudante General',
            'base_salary' => 16.0000,
            'social_load_pct' => 15.0000,
            'hourly_cost' => 18.4000,
        ]);
    }
}
