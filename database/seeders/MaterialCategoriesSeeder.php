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
        foreach (MaterialCategoryEnum::cases() as $category) {
            MaterialCategory::updateOrCreate(
                ['code' => $category->value],
                ['display_name' => $category->label()]
            );
        }
    }
}
