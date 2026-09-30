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
        Schema::create('discount_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')->constrained('discounts');
            $table->foreignId('transaction_id')->constrained('transactions');
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('discount_amount', 15, 2);
            $table->enum('status', ['USED', 'REVERTED'])->default('USED');
            $table->timestamp('created_at')->useCurrent();

            // ---- INDEX untuk cek limit per user ----
            $table->index(['discount_id', 'user_id'], 'idx_usage_user');

            // ---- UNIQUE constraint: 1 diskon hanya sekali per order ----
            $table->unique(['discount_id', 'transaction_id'], 'uq_discount_transaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_usages');
    }
};
