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
        QuoteStatus::factory()->draft()->create();
        QuoteStatus::factory()->approved()->create();
        QuoteStatus::factory()->canceled()->create();
        QuoteStatus::factory()->closedByAmendment()->create();
    }
}
