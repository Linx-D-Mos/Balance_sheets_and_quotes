<?php

namespace Database\Factories;

use App\Enums\QuoteStatusEnum;
use App\Models\Project;
use App\Models\Quote;
use App\Models\QuoteStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quote>
 */
class QuoteFactory extends Factory
{
    protected $model = Quote::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'quote_status_id' => fn () => QuoteStatus::where('code', QuoteStatusEnum::DRAFT)->first()?->id ?? QuoteStatus::factory(),
            'parent_quote_id' => null,
            'title' => 'Cotización Inicial',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(12),
            'work_weekends' => false,
            'amendment_level' => 0,
            'total_hours' => 80,
            'direct_labor_cost' => 1840.0000,
            'direct_materials_cost' => 500.0000,
            'direct_cost' => 2340.0000,
            'overhead_rate_applied' => 11.2500,
            'overtime_multiplier_applied' => 1.5000,
            'overhead_cost' => 900.0000,
            'equilibrium_cost' => 3240.0000,
            'margin_applied' => 20.0000,
            'total_price' => 4050.0000,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'quote_status_id' => fn () => QuoteStatus::where('code', QuoteStatusEnum::APPROVED)->first()?->id,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'quote_status_id' => fn () => QuoteStatus::where('code', QuoteStatusEnum::CANCELED)->first()?->id,
        ]);
    }

    public function closedAmendment(): static
    {
        return $this->state(fn (array $attributes) => [
            'quote_status_id' => fn () => QuoteStatus::where('code', QuoteStatusEnum::CLOSED_BY_AMENDMENT)->first()?->id,
        ]);
    }

}
