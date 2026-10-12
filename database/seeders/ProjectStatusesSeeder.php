<?php

namespace Database\Seeders;

use App\Enums\ProjectStatusEnum;
use App\Models\ProjectStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (ProjectStatusEnum::cases() as $state) {
            ProjectStatus::updateOrCreate(
                ['code' => $state->value],
                [
                    'display_name' => $state->getLabel(),
                    'icon' => $state->getIcon(),
                    'bg_color' => $state->getColor(),
                    'bg_text' => $state->bgText()
                ]
            );
        }
    }
}
