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
        Schema::create('quote_labor_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->cascadeOnDelete();
            $table->foreignId('labor_role_id')->constrained('labor_roles')->restrictOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->restrictOnDelete();
            $table->string('worker_name_placeholder')->nullable();
            $table->integer('estimated_hours_regular')->default(0);
            $table->integer('estimated_hours_extra')->default(0);
            $table->decimal('hourly_rate_at_estimation', 12, 4)->default(0);
            $table->decimal('estimated_subtotal', 12, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_labor_assignments');
    }
};
