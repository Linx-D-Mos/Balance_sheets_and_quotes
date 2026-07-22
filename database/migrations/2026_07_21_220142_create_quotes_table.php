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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->restrictOnDelete();
            $table->foreignId('quote_status_id')->constrained('quote_statuses')->restrictOnDelete();
            $table->foreignId('parent_quote_id')->nullable()->constrained('quotes')->restrictOnDelete();
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('work_weekends')->default(false);
            $table->integer('amendment_level')->default(0);
            $table->integer('total_hours')->default(0);
            $table->decimal('direct_labor_cost', 12, 4)->default(0);
            $table->decimal('direct_materials_cost', 12, 4)->default(0);
            $table->decimal('direct_cost', 12, 4)->default(0);
            $table->decimal('overhead_rate_applied', 12, 4)->default(0);
            $table->decimal('overtime_multiplier_applied', 12, 4)->default(0);
            $table->decimal('overhead_cost', 12, 4)->default(0);
            $table->decimal('equilibrium_cost', 12, 4)->default(0);
            $table->decimal('margin_applied', 12, 4)->default(0);
            $table->decimal('total_price', 12, 4)->default(0);
            $table->timestamps();

            $table->index(['project_id', 'quote_status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
