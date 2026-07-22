<?php

namespace Database\Seeders;

use App\Models\GlobalSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlobalSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GlobalSetting::updateOrCreate(
            ['id' => 1],
            [
                'standard_monthly_hours' => 160.0000,
                'default_overhead_rate_applied' => 0.1500,
                'default_profit_margin' => 0.2000,
                'overtime_multiplier' => 1.5000,
            ]
        );
    }
}
