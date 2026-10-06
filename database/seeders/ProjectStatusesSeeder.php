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
                    'display_name' => $state->label(),
                    'icon' => $state->icon(),
                    'bg_color' => $state->bgColor(),
                    'bg_text' => $state->bgText()
                ]
            );
        }
    }
}
