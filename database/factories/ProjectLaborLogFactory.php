<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\LaborRole;
use App\Models\Project;
use App\Models\ProjectLaborLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectLaborLog>
 */
class ProjectLaborLogFactory extends Factory
{
    protected $model = ProjectLaborLog::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'quote_labor_assignment_id' => null,
            'employee_id' => Employee::factory(),
            'labor_role_id' => LaborRole::factory(),
            'annulled_by_user_id' => null,
            'actual_hours_regular' => 8,
            'actual_hours_extra' => 0,
            'hourly_rate_actual' => 23.0000,
            'overtime_multiplier_applied' => 1.5000,
            'actual_subtotal' => 184.0000,
            'logged_at' => now(),
            'is_annulled' => false,
            'annulled_at' => null,
            'annulment_reason' => null,
        ];
    }

    public function annulled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_annulled' => true,
            'annulled_at' => now(),
            'annulment_reason' => 'Error de digitación en horas extras',
        ]);
    }
}
