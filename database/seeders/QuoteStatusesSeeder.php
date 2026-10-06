<?php

namespace Database\Seeders;

use App\Enums\QuoteStatusEnum;
use App\Models\QuoteStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuoteStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (QuoteStatusEnum::cases() as $state) {
            QuoteStatus::updateOrCreate(
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
