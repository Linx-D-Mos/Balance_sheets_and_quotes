<?php

namespace Database\Seeders;

use App\Models\LaborRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LaborRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LaborRole::factory()->painter()->create();
        LaborRole::factory()->foreman()->create();
        LaborRole::factory()->helper()->create();
    }
}
