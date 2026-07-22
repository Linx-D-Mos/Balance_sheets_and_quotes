<?php

namespace Database\Factories;

use App\Models\FixedExpense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FixedExpense>
 */
class FixedExpenseFactory extends Factory
{

    protected $model = FixedExpense::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'concept' => $this->faker->company() . ' Expense',
            'amount' => $this->faker->randomFloat(4,200,2500),
            'is_active' => true,
        ];
    }

    public function rent(): static
    {
        return $this->state(fn (array $attributes) => [
            'concept' => 'Renta de Bodega / Oficina',
            'amount' => 1200.0000,
        ]);
    }

    public function insurance(): static
    {
        return $this->state(fn (array $attributes) => [
            'concept' => 'Seguro Liability Comercial',
            'amount' => 450.0000,
        ]);
    }

    public function software(): static
    {
        return $this->state(fn (array $attributes) => [
            'concept' => 'Suscripciones Software ERP/CRM',
            'amount' => 150.0000,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
