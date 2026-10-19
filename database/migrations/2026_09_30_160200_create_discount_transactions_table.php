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
        Schema::create('discount_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')->constrained('discounts')->onDelete('cascade');
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->enum('level', ['PRODUCT', 'TRANSACTION']);
            $table->string('discount_name')->nullable();
            $table->string('discount_code', 50)->nullable();
            $table->enum('value_type', ['PERCENTAGE', 'FIXED_AMOUNT']);
            $table->decimal('value', 15, 2);
            $table->decimal('applied_amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_transactions');
    }
};
