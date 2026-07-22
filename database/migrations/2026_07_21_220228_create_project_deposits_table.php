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
        Schema::create('project_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->restrictOnDelete();
            $table->foreignId('annulled_by_user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->decimal('amount', 12, 4);
            $table->string('payment_method');
            $table->date('received_at');
            $table->string('reference_number')->nullable();
            $table->boolean('is_annulled')->default(false);
            $table->timestamp('annulled_at')->nullable();
            $table->string('annulment_reason')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'received_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_deposits');
    }
};
