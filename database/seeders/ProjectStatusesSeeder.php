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
        ProjectStatus::factory()->draft()->create();
        ProjectStatus::factory()->inProgress()->create();
        ProjectStatus::factory()->completed()->create();
        ProjectStatus::factory()->cancelled()->create();
    }
}
