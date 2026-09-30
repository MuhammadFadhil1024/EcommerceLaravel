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
        Schema::create('discount_targets', function (Blueprint $table) {
            $table->id();
             $table->foreignId('discount_id')
                  ->constrained('discounts')
                  ->cascadeOnDelete(); // ON DELETE CASCADE
            $table->enum('target_type', ['PRODUCT', 'CATEGORY']);
            $table->unsignedBigInteger('target_id');

            // ---- INDEX untuk lookup "produk ini punya diskon apa" ----
            $table->index(['target_type', 'target_id'], 'idx_discount_targets_lookup');

            // ---- UNIQUE constraint: cegah target ganda ----
            $table->unique(['discount_id', 'target_type', 'target_id'], 'uq_discount_target');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_targets');
    }
};
