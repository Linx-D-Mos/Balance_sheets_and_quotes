<?php

namespace Database\Factories;

use App\Enums\PaymentMethodEnum;
use App\Models\Project;
use App\Models\ProjectDeposit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectDeposit>
 */
class ProjectDepositFactory extends Factory
{
    protected $model = ProjectDeposit::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'annulled_by_user_id' => null,
            'amount' => 1500.0000,
            'payment_method' => PaymentMethodEnum::ZELLE,
            'received_at' => now(),
            'reference_number' => 'ZEL-982130',
            'is_annulled' => false,
            'annulled_at' => null,
            'annulment_reason' => null,
        ];
    }
}
