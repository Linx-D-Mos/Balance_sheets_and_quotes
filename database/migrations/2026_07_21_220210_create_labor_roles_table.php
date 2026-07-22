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
        Schema::create('labor_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('base_salary', 12, 4);
            $table->decimal('social_load_pct', 12, 4);
            $table->decimal('hourly_cost', 12, 4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labor_roles');
    }
};
