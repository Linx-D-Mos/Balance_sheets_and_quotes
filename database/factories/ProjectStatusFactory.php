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
    protected $model = ProjectStatus::class;
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
            'display_name' => ProjectStatusEnum::DRAFT->getLabel(),
            'code' => ProjectStatusEnum::DRAFT,
            'icon' => ProjectStatusEnum::DRAFT->getIcon(),
            'bg_color' => ProjectStatusEnum::DRAFT->getColor(),
            'bg_text' => ProjectStatusEnum::DRAFT->bgText(),
        ]);
    }
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'display_name' => ProjectStatusEnum::IN_PROGRESS->getLabel(),
            'code' => ProjectStatusEnum::IN_PROGRESS,
            'icon' => ProjectStatusEnum::IN_PROGRESS->getIcon(),
            'bg_color' => ProjectStatusEnum::IN_PROGRESS->getColor(),
            'bg_text' => ProjectStatusEnum::IN_PROGRESS->bgText(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'display_name' => ProjectStatusEnum::COMPLETED->getLabel(),
            'code' => ProjectStatusEnum::COMPLETED,
            'icon' => ProjectStatusEnum::COMPLETED->getIcon(),
            'bg_color' => ProjectStatusEnum::COMPLETED->getColor(),
            'bg_text' => ProjectStatusEnum::COMPLETED->bgText(),
        ]);
    }

    public function canceled(): static
    {
        return $this->state(fn (array $attributes) => [
            'display_name' => ProjectStatusEnum::CANCELED->getLabel(),
            'code' => ProjectStatusEnum::CANCELED,
            'icon' => ProjectStatusEnum::CANCELED->getIcon(),
            'bg_color' => ProjectStatusEnum::CANCELED->getColor(),
            'bg_text' => ProjectStatusEnum::CANCELED->bgText(),
        ]);
    }

}
