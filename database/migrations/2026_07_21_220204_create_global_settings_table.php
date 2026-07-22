<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('global_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('standard_monthly_hours', 12, 4);
            $table->decimal('default_overhead_rate_applied', 12, 4);
            $table->decimal('default_profit_margin', 12, 4);
            $table->decimal('overtime_multiplier', 12, 4)->default(1.5000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_settings');
    }
};
