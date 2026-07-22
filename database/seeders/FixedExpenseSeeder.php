<?php

namespace Database\Seeders;

use App\Models\FixedExpense;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FixedExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FixedExpense::factory()->rent()->create();
        FixedExpense::factory()->insurance()->create();
        FixedExpense::factory()->software()->create();
    }
}
