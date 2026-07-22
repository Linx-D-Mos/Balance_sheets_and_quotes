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
        Schema::create('project_material_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->restrictOnDelete();
            $table->foreignId('material_category_id')->constrained('material_categories')->restrictOnDelete();
            $table->foreignId('quote_material_item_id')->nullable()->constrained('quote_material_items')->restrictOnDelete();
            $table->foreignId('annulled_by_user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('concept');
            $table->string('store')->nullable();
            $table->string('payment_method');
            $table->string('buyer_name')->nullable();
            $table->decimal('actual_quantity', 12, 4);
            $table->decimal('actual_unit_price', 12, 4);
            $table->decimal('actual_subtotal', 12, 4);
            $table->date('purchased_at');
            $table->boolean('is_annulled')->default(false);
            $table->timestamp('annulled_at')->nullable();
            $table->string('annulment_reason')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'purchased_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_material_purchases');
    }
};
