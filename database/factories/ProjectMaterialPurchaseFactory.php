<?php

namespace Database\Factories;

use App\Enums\MaterialCategoryEnum;
use App\Enums\PaymentMethodEnum;
use App\Models\MaterialCategory;
use App\Models\Project;
use App\Models\ProjectMaterialPurchase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectMaterialPurchase>
 */
class ProjectMaterialPurchaseFactory extends Factory
{
   protected $model = ProjectMaterialPurchase::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'material_category_id' => fn () => MaterialCategory::where('code', 'budgeted')->first()?->id ?? MaterialCategory::factory(),
            'quote_material_item_id' => null,
            'annulled_by_user_id' => null,
            'concept' => 'Pintura Blanca 5 Galones',
            'store' => 'Sherwin-Williams',
            'payment_method' => PaymentMethodEnum::CREDIT_CARD,
            'buyer_name' => 'Carlos Delgado',
            'actual_quantity' => 2.0000,
            'actual_unit_price' => 120.0000,
            'actual_subtotal' => 240.0000,
            'purchased_at' => now(),
            'is_annulled' => false,
            'annulled_at' => null,
            'annulment_reason' => null,
        ];
    }

    public function unbudgeted(): static
    {
        return $this->state(fn (array $attributes) => [
            'material_category_id' => fn () => MaterialCategory::where('code', MaterialCategoryEnum::UNBUDGETED)->first()?->id,
            'quote_material_item_id' => null,
            'concept' => 'Cinta de Enmascarar Adicional',
        ]);
    }
}
