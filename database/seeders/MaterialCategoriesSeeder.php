<?php

namespace Database\Seeders;

use App\Enums\MaterialCategoryEnum;
use App\Models\MaterialCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (MaterialCategoryEnum::cases() as $state) {
            MaterialCategory::updateOrCreate(
                ['code' => $state->value],
                [
                    'display_name' => $state->label(),
                    'icon' => $state->icon(),
                    'bg_color' => $state->bgColor(),
                    'bg_text' => $state->bgText()
                ]
            );
        }
    }
}
