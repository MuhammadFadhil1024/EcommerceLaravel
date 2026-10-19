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
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('product_discount_total', 15, 2)->default(0);
            $table->decimal('transaction_discount_total', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('product_discount_total');
            $table->dropColumn('transaction_discount_total');
            $table->dropColumn('grand_total');
        });
    }
};
