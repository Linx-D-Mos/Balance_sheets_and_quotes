<?php

namespace Database\Factories;

use App\Enums\ProjectStatusEnum;
use App\Models\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectStatus>
 */
class ProjectStatusFactory extends Factory
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
            'code' => $this->faker->unique()->randomElement(ProjectStatusEnum::cases()),
            'icon' => 'mdi-circle-outline',
            'bg_color' => $this->faker->hexColor(),
            'bg_text' => '#ffffff',
        ];
    }
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'display_name' => ProjectStatusEnum::DRAFT->label(),
            'code' => ProjectStatusEnum::DRAFT,
            'icon' => 'mdi-circle-outline',
            'bg_color' => '#6b7280',
            'bg_text' => '#ffffff',
        ]);
    }
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'display_name' => ProjectStatusEnum::IN_PROGRESS->label(),
            'code' => ProjectStatusEnum::IN_PROGRESS,
            'icon' => 'mdi-progress-clock',
            'bg_color' => '#3b82f6',
            'bg_text' => '#ffffff',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'display_name' => ProjectStatusEnum::COMPLETED->label(),
            'code' => ProjectStatusEnum::COMPLETED,
            'icon' => 'mdi-check-circle-outline',
            'bg_color' => '#22c55e',
            'bg_text' => '#ffffff',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'display_name' => ProjectStatusEnum::CANCELLED->label(),
            'code' => ProjectStatusEnum::CANCELLED,
            'icon' => 'mdi-cancel',
            'bg_color' => '#ef4444',
            'bg_text' => '#ffffff',
        ]);
    }

}
