<?php

namespace Database\Factories;

use App\Enums\ProjectStatusEnum;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{

    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'project_status_id' => fn () => ProjectStatus::where('code', ProjectStatusEnum::DRAFT)->first()?->id ?? ProjectStatus::factory(),
            'code' => 'PRJ-' . $this->faker->unique()->numberBetween(1000, 9999),
            'title' => 'Proyecto ' . $this->faker->streetName(),
            'address' => $this->faker->address(),
            'actual_start_date' => null,
            'actual_end_date' => null,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'project_status_id' => fn () => ProjectStatus::where('code', ProjectStatusEnum::IN_PROGRESS)->first()?->id,
            'actual_start_date' => now()->subDays(5),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'project_status_id' => fn () => ProjectStatus::where('code', ProjectStatusEnum::COMPLETED)->first()?->id,
            'actual_start_date' => now()->subDays(30),
            'actual_end_date' => now()->subDays(2),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'project_status_id' => fn () => ProjectStatus::where('code', ProjectStatusEnum::CANCELLED)->first()?->id,
        ]);
    }
}
