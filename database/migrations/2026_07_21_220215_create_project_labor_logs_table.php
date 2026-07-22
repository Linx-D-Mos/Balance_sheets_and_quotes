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
        Schema::create('project_labor_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->restrictOnDelete();
            $table->foreignId('quote_labor_assignment_id')->nullable()->constrained('quote_labor_assignments')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->foreignId('labor_role_id')->constrained('labor_roles')->restrictOnDelete();
            $table->foreignId('annulled_by_user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->integer('actual_hours_regular')->default(0);
            $table->integer('actual_hours_extra')->default(0);
            $table->decimal('hourly_rate_actual', 12, 4);
            $table->decimal('overtime_multiplier_applied', 12, 4);
            $table->decimal('actual_subtotal', 12, 4);
            $table->date('logged_at');
            $table->boolean('is_annulled')->default(false);
            $table->timestamp('annulled_at')->nullable();
            $table->string('annulment_reason')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'logged_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_labor_logs');
    }
};
