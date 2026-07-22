<?php

namespace Database\Factories;

use App\Enums\QuoteStatusEnum;
use App\Models\QuoteStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuoteStatus>
 */
class QuoteStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'display_name' => $this->faker->unique()->words(2, true),
            'code' => $this->faker->unique()->randomElement(QuoteStatusEnum::cases()),,
            'icon' => 'mdi-circle-outline',
            'bg_color' => $this->faker->hexColor(),
            'bg_text' => '#ffffff',
        ];
    }
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'display_name' => QuoteStatusEnum::DRAFT->label(),
            'code' => QuoteStatusEnum::DRAFT,
            'icon' => 'mdi-file-document-outline',
            'bg_color' => '#6b7280',
            'bg_text' => '#ffffff',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'display_name' => QuoteStatusEnum::APPROVED->label(),
            'code' => QuoteStatusEnum::APPROVED,
            'icon' => 'mdi-check-circle-outline',
            'bg_color' => '#22c55e',
            'bg_text' => '#ffffff',
        ]);
    }

    public function canceled(): static
    {
        return $this->state(fn(array $attributes) => [
            'display_name' => QuoteStatusEnum::CANCELED->label(),
            'code' => QuoteStatusEnum::CANCELED,
            'icon' => 'mdi-cancel',
            'bg_color' => '#ef4444',
            'bg_text' => '#ffffff',
        ]);
    }

    public function closedByAmendment(): static
    {
        return $this->state(fn(array $attributes) => [
            'display_name' => QuoteStatusEnum::CLOSED_BY_AMENDMENT->label(),
            'code' => QuoteStatusEnum::CLOSED_BY_AMENDMENT,
            'icon' => 'mdi-file-edit-outline',
            'bg_color' => '#f97316',
            'bg_text' => '#ffffff',
        ]);
    }
}
